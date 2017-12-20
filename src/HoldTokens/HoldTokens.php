<?php

namespace Seatsio\HoldTokens;

use JsonMapper;

class HoldTokens
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
     * @return HoldToken
     */
    public function create()
    {
        $res = $this->client->request('POST', '/hold-tokens');
        $json = \GuzzleHttp\json_decode($res->getBody());
        $mapper = new JsonMapper();
        return $mapper->map($json, new HoldToken());
    }

    /**
     * @param $holdToken string
     * @param $expiresInMintes int
     * @return HoldToken
     */
    public function update($holdToken, $expiresInMintes)
    {
        $request = new \stdClass();
        $request->expiresInMinutes = $expiresInMintes;
        $res = $this->client->request('PUT', '/hold-tokens/' . $holdToken, ['json' => $request]);
        $json = \GuzzleHttp\json_decode($res->getBody());
        $mapper = new JsonMapper();
        return $mapper->map($json, new HoldToken());
    }

}