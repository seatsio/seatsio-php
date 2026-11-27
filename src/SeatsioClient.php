<?php

namespace Seatsio;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use InvalidArgumentException;
use Seatsio\Charts\Charts;
use Seatsio\EventLog\EventLog;
use Seatsio\Events\Events;
use Seatsio\HoldTokens\HoldTokens;
use Seatsio\Http\IdempotentRetries;
use Seatsio\Http\RetryClock;
use Seatsio\Http\SharedState;
use Seatsio\Reports\Charts\ChartReports;
use Seatsio\Reports\Events\EventReports;
use Seatsio\Reports\Usage\UsageReports;
use Seatsio\Seasons\Seasons;
use Seatsio\TicketBuyers\TicketBuyers;
use Seatsio\Workspaces\Workspaces;

class SeatsioClient
{
    public const IDEMPOTENCY_KEY_HEADER = IdempotentRetries::IDEMPOTENCY_KEY_HEADER;

    private const V4_UUID = '/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/iD';

    public $client;

    private Region $region;
    private string $secretKey;
    private ?string $workspaceKey;
    private SharedState $state;

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
        $this->initialize($region, $secretKey, $workspaceKey, new SharedState($maxRetries, new RetryClock()), null);
    }

    public function withIdempotencyKey(?string $idempotencyKey): SeatsioClient
    {
        if ($idempotencyKey === null || !preg_match(self::V4_UUID, $idempotencyKey)) {
            throw new InvalidArgumentException("Idempotency key $idempotencyKey is not a v4 UUID");
        }
        $copy = clone $this;
        $copy->initialize($this->region, $this->secretKey, $this->workspaceKey, $this->state, $idempotencyKey);
        return $copy;
    }

    private function initialize(Region $region, string $secretKey, ?string $workspaceKey, SharedState $state, ?string $idempotencyKey): void
    {
        $this->region = $region;
        $this->secretKey = $secretKey;
        $this->workspaceKey = $workspaceKey;
        $this->state = $state;
        $client = new Client($this->clientConfig($secretKey, $workspaceKey, $region->url(), $state, $idempotencyKey));
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

    private function clientConfig($secretKey, $workspaceKey, $baseUrl, SharedState $state, ?string $idempotencyKey)
    {
        $stack = HandlerStack::create();
        $stack->push(new IdempotentRetries($state, $idempotencyKey));
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

}
