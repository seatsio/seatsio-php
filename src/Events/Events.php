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
     * @param $chartKey string
     * @param $eventKey string
     * @param $bookWholeTables boolean
     * @return Event
     */
    public function create($chartKey, $eventKey = null, $bookWholeTables = null)
    {
        $request = new \stdClass();
        $request->chartKey = $chartKey;
        if($eventKey !== null) {
            $request->eventKey = $eventKey;
        }
        if($bookWholeTables !== null) {
            $request->bookWholeTables = $bookWholeTables;
        }
        $res = $this->client->request('POST', '/events', ['json' => $request]);
        $json = \GuzzleHttp\json_decode($res->getBody());
        $mapper = new JsonMapper();
        return $mapper->map($json, new Event());
    }

    /**
     * @param $key string
     * @return Event
     */
    public function retrieve($key)
    {
        $res = $this->client->request('GET', '/events/' . $key);
        $json = \GuzzleHttp\json_decode($res->getBody());
        $mapper = new JsonMapper();
        return $mapper->map($json, new Event());
    }

}