<?php

namespace Seatsio\HoldTokens;

use GuzzleHttp\Client;
use Seatsio\SeatsioJsonMapper;
use stdClass;

class HoldTokens
{

    /**
     * @var Client
     */
    private $client;

    public function __construct($client)
    {
        $this->client = $client;
    }

    /**
     * @param $expiresInMinutes int
     * @return HoldToken
     */
    public function create($expiresInMinutes = null)
    {
        $request = new stdClass();
        if (!is_null($expiresInMinutes)) {
            $request->expiresInMinutes = $expiresInMinutes;
        }
        $res = $this->client->post('/hold-tokens', ['json' => $request]);
        $json = \GuzzleHttp\json_decode($res->getBody());
        $mapper = SeatsioJsonMapper::create();
        return $mapper->map($json, new HoldToken());
    }

    /**
     * @param $holdToken string
     * @param $minutes int
     * @return HoldToken
     */
    public function expireInMinutes($holdToken, $minutes)
    {
        $request = new stdClass();
        $request->expiresInMinutes = $minutes;
        $res = $this->client->post('/hold-tokens/' . $holdToken, ['json' => $request]);
        $json = \GuzzleHttp\json_decode($res->getBody());
        $mapper = SeatsioJsonMapper::create();
        return $mapper->map($json, new HoldToken());
    }

    /**
     * @param $holdToken string
     * @return HoldToken
     */
    public function retrieve($holdToken)
    {
        $res = $this->client->get('/hold-tokens/' . $holdToken);
        $json = \GuzzleHttp\json_decode($res->getBody());
        $mapper = SeatsioJsonMapper::create();
        return $mapper->map($json, new HoldToken());
    }

}
