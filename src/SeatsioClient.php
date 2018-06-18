<?php

namespace Seatsio;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use Psr\Http\Message\ResponseInterface;
use Seatsio\Charts\Charts;
use Seatsio\EventReports\EventReports;
use Seatsio\Events\Events;
use Seatsio\HoldTokens\HoldTokens;
use Seatsio\Subaccounts\Subaccounts;

class SeatsioClient
{
    private $client;

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
     * @var Subaccounts
     */
    public $subaccounts;

    /**
     * @var HoldTokens
     */
    public $holdTokens;

    public function __construct($secretKey, $baseUrl = 'https://api.seatsio.net/')
    {
        $stack = HandlerStack::create();
        $stack->push(self::errorHandler());
        $client = new Client([
            'base_uri' => $baseUrl,
            'auth' => [$secretKey, null],
            'http_errors' => false,
            'handler' => $stack,
            'headers' => ['Accept-Encoding' => 'gzip']
        ]);
        $this->charts = new Charts($client);
        $this->events = new Events($client);
        $this->eventReports = new EventReports($client);
        $this->subaccounts = new Subaccounts($client);
        $this->holdTokens = new HoldTokens($client);
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