<?php

namespace Seatsio\Subaccounts;

use JsonMapper;

class Subaccounts
{

    /**
     * @var \GuzzleHttp\Client
     */
    private $client;
    private $pageSize;

    public function __construct($client, $pageSize)
    {
        $this->client = $client;
        $this->pageSize = $pageSize;
    }

    /**
     * @return Subaccount
     */
    public function create()
    {
        $res = $this->client->request('POST', '/subaccounts');
        $json = \GuzzleHttp\json_decode($res->getBody());
        $mapper = new JsonMapper();
        return $mapper->map($json, new Subaccount());
    }

}