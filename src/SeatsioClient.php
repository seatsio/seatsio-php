<?php

namespace Seatsio;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use Psr\Http\Message\ResponseInterface;
use Seatsio\Charts\Charts;
use Seatsio\Events\Events;
use Seatsio\HoldTokens\HoldTokens;
use Seatsio\Reports\Charts\ChartReports;
use Seatsio\Reports\Events\EventReports;
use Seatsio\Reports\Usage\UsageReports;
use Seatsio\Subaccounts\Subaccounts;
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
     * @var EventReports
     */
    public $eventReports;

    /**
     * @var ChartReports
     */
    public $chartReports;

    /**
     * @var Subaccounts
     */
    public $subaccounts;

    /**
     * @var Workspaces
     */
    public $workspaces;

    /**
     * @var HoldTokens
     */
    public $holdTokens;

    public function __construct($region, $secretKey, $workspaceKey = null)
    {
        $client = new Client($this->clientConfig($secretKey, $workspaceKey, $region->url()));
        $this->client = $client;
        $this->charts = new Charts($client);
        $this->events = new Events($client);
        $this->eventReports = new EventReports($client);
        $this->chartReports = new ChartReports($client);
        $this->usageReports = new UsageReports($client);
        $this->subaccounts = new Subaccounts($client);
        $this->workspaces = new Workspaces($client);
        $this->holdTokens = new HoldTokens($client);
    }

    private function clientConfig($secretKey, $workspaceKey, $baseUrl)
    {
        $stack = HandlerStack::create();
        $stack->push(self::errorHandler());
        $stack->push(Middleware::retry(
            function ($numRetries, $request, $response) {
                return $numRetries < 4 && $response->getStatusCode() == 429;
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
            'headers' => ['Accept-Encoding' => 'gzip']
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
                        throw new SeatsioException($request, $response);
                    }
                );
            };
        };
    }

}
