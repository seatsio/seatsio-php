<?php

namespace Seatsio;

use GuzzleHttp\Client;
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
        $this->client = new Client([
            'base_uri' => $baseUrl,
            'auth' => [$secretKey, null]
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

}