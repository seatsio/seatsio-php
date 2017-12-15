<?php

namespace Seatsio;

use GuzzleHttp\Client;

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
}