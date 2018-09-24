<?php

namespace Seatsio\Accounts;

use Seatsio\Charts\Chart;
use Seatsio\PageFetcher;
use Seatsio\SeatsioJsonMapper;

class Accounts
{

    /**
     * @var \GuzzleHttp\Client
     */
    private $client;

    public function __construct($client)
    {
        $this->client = $client;
    }

    /**
     * @return Account
     */
    public function retrieveMyAccount()
    {
        $res = $this->client->get('/accounts/me');
        return \GuzzleHttp\json_decode($res->getBody());
    }

}