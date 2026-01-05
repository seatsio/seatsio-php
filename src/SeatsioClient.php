<?php

namespace Seatsio;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
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
    public $client;

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
        $client = new Client($this->clientConfig($secretKey, $workspaceKey, $region->url(), $maxRetries));
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

    private function clientConfig($secretKey, $workspaceKey, $baseUrl, $maxRetries)
    {
        $stack = HandlerStack::create();
        $stack->push(self::errorHandler());
        $stack->push(Middleware::retry(
            function ($numRetries, $request, ?Response $response, ?\Exception $exception) use ($maxRetries) {
                if ($exception instanceof ConnectException) {
                    throw SeatsioException::fromException($request, $exception);
                }
                return $numRetries < $maxRetries - 1 && $response->getStatusCode() == 429;
            },
            function ($numRetries) {
                return pow(2, $numRetries + 2) * 100;
            }
        ));
        $config = [
            'base_uri' => $baseUrl,
            'auth' => [$secretKey, null],
            'http_errors' => false,
            'handler' => $stack,
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
