<?php

namespace Seatsio;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\TransferException;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use InvalidArgumentException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Seatsio\Charts\Charts;
use Seatsio\EventLog\EventLog;
use Seatsio\Events\Events;
use Seatsio\HoldTokens\HoldTokens;
use Seatsio\Reports\Charts\ChartReports;
use Seatsio\Reports\Events\EventReports;
use Seatsio\Reports\Usage\UsageReports;
use Seatsio\Seasons\Seasons;
use Seatsio\TicketBuyers\TicketBuyers;
use Seatsio\Workspaces\Workspaces;

class SeatsioClient
{
    public const IDEMPOTENCY_KEY_HEADER = 'Idempotency-Key';

    private const SAFE_METHODS = ['GET', 'HEAD', 'OPTIONS', 'TRACE'];
    private const V4_UUID = '/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/iD';

    public $client;

    private Region $region;
    private string $secretKey;
    private ?string $workspaceKey;
    private RetrySettings $retrySettings;

    /**
     * @var Charts
     */
    public $charts;

    /**
     * @var Events
     */
    public $events;

    /**
     * @var Seasons
     */
    public $seasons;

    /**
     * @var EventReports
     */
    public $eventReports;

    /**
     * @var ChartReports
     */
    public $chartReports;

    /**
     * @var UsageReports
     */
    public $usageReports;

    /**
     * @var Workspaces
     */
    public $workspaces;

    /**
     * @var HoldTokens
     */
    public $holdTokens;

    /**
     * @var EventLog
     */
    public $eventLog;

    /**
     * @var TicketBuyers
     */
    public $ticketBuyers;

    public function __construct(Region $region, string $secretKey, ?string $workspaceKey = null, int $maxRetries = 5)
    {
        $this->initialize($region, $secretKey, $workspaceKey, new RetrySettings($maxRetries), null);
    }

    public function retryNonIdempotentRequests(bool $retryNonIdempotentRequests): SeatsioClient
    {
        $this->retrySettings->setRetryNonIdempotentRequests($retryNonIdempotentRequests);
        return $this;
    }

    public function withIdempotencyKey(string $idempotencyKey): SeatsioClient
    {
        if (!preg_match(self::V4_UUID, $idempotencyKey)) {
            throw new InvalidArgumentException("Idempotency key $idempotencyKey is not a v4 UUID");
        }
        $copy = clone $this;
        $copy->initialize($this->region, $this->secretKey, $this->workspaceKey, $this->retrySettings->inherit(), $idempotencyKey);
        return $copy;
    }

    private function initialize(Region $region, string $secretKey, ?string $workspaceKey, RetrySettings $retrySettings, ?string $idempotencyKey): void
    {
        $this->region = $region;
        $this->secretKey = $secretKey;
        $this->workspaceKey = $workspaceKey;
        $this->retrySettings = $retrySettings;
        $client = new Client($this->clientConfig($secretKey, $workspaceKey, $region->url(), $retrySettings, $idempotencyKey));
        $this->client = $client;
        $this->charts = new Charts($client);
        $this->events = new Events($client);
        $this->seasons = new Seasons($client, $this);
        $this->eventReports = new EventReports($client);
        $this->chartReports = new ChartReports($client);
        $this->usageReports = new UsageReports($client);
        $this->workspaces = new Workspaces($client);
        $this->holdTokens = new HoldTokens($client);
        $this->eventLog = new EventLog($client);
        $this->ticketBuyers = new TicketBuyers($client);
    }

    private function clientConfig($secretKey, $workspaceKey, $baseUrl, RetrySettings $retrySettings, ?string $idempotencyKey)
    {
        $stack = HandlerStack::create();
        $stack->push(Middleware::mapRequest(function (RequestInterface $request) use ($idempotencyKey) {
            if (self::isSafe($request) || $request->hasHeader(self::IDEMPOTENCY_KEY_HEADER)) {
                return $request;
            }
            return $request->withHeader(self::IDEMPOTENCY_KEY_HEADER, $idempotencyKey ?? self::randomUuid());
        }));
        $stack->push(self::errorHandler());
        $stack->push(Middleware::retry(
            function ($numRetries, $request, ?ResponseInterface $response, ?\Throwable $exception) use ($retrySettings) {
                $canRetry = $numRetries < $retrySettings->maxRetries - 1;
                $connectionFailed = $response === null;
                if ($connectionFailed) {
                    if (!($exception instanceof TransferException)) {
                        return false;
                    }
                    if ($canRetry && $retrySettings->retryNonIdempotentRequests() && !self::isSafe($request)) {
                        return true;
                    }
                    throw SeatsioException::fromException($request, $exception);
                }
                return $canRetry && ($response->getStatusCode() == 429 || SeatsioException::isIdempotencyKeyInUse($response));
            },
            function ($numRetries) {
                return pow(2, $numRetries + 2) * 100;
            }
        ));
        $config = [
            'base_uri' => $baseUrl,
            'auth' => [$secretKey, ''],
            'http_errors' => false,
            'handler' => $stack,
            'timeout' => 10,
            'headers' => [
                'Accept-Encoding' => 'gzip',
                'X-Client-Lib' => 'php'
            ]
        ];
        if ($workspaceKey) {
            $config['headers']['X-Workspace-Key'] = $workspaceKey;
        }
        return $config;
    }

    private static function isSafe(RequestInterface $request): bool
    {
        return in_array(strtoupper($request->getMethod()), self::SAFE_METHODS, true);
    }

    private static function randomUuid(): string
    {
        $bytes = random_bytes(16);
        $bytes[6] = chr((ord($bytes[6]) & 0x0f) | 0x40);
        $bytes[8] = chr((ord($bytes[8]) & 0x3f) | 0x80);
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($bytes), 4));
    }

    private function errorHandler()
    {
        return function (callable $handler) {
            return function ($request, array $options) use ($handler) {
                return $handler($request, $options)->then(
                    function (ResponseInterface $response) use ($request, $handler) {
                        $code = $response->getStatusCode();
                        if ($code < 400) {
                            return $response;
                        }
                        throw SeatsioException::from($request, $response);
                    }
                );
            };
        };
    }

}
