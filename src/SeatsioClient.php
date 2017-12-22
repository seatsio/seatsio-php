<?php

namespace Seatsio;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use Psr\Http\Message\ResponseInterface;
use Seatsio\Charts\Charts;
use Seatsio\Events\Events;
use Seatsio\HoldTokens\HoldTokens;
use Seatsio\Subaccounts\Subaccounts;

class SeatsioClient
{
    private $client;
    private $pageSize;

    public function __construct($secretKey, $baseUrl = 'https://api.seats.io/')
    {
        $stack = HandlerStack::create();
        $stack->push(self::errorHandler());
        $this->client = new Client([
            'base_uri' => $baseUrl,
            'auth' => [$secretKey, null],
            'http_errors' => false,
            'handler' => $stack
        ]);
    }

    public function setPageSize($pageSize)
    {
        $this->pageSize = $pageSize;
    }

    /**
     * @return Charts
     */
    public function charts()
    {
        return new Charts($this->client, $this->pageSize);
    }

    /**
     * @return Events
     */
    public function events()
    {
        return new Events($this->client, $this->pageSize);
    }

    /**
     * @return Subaccounts
     */
    public function subaccounts()
    {
        return new Subaccounts($this->client, $this->pageSize);
    }

    /**
     * @return HoldTokens
     */
    public function holdTokens()
    {
        return new HoldTokens($this->client, $this->pageSize);
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