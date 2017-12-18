<?php

namespace Seatsio\Events;

use JsonMapper;

class Events
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
     * @return Event
     */
    public function create($chartKey)
    {
        $request = new \stdClass();
        $request->chartKey = $chartKey;
        $res = $this->client->request('POST', '/events', ['json' => $request]);
        $json = \GuzzleHttp\json_decode($res->getBody());
        $mapper = new JsonMapper();
        return $mapper->map($json, new Event());
    }

}