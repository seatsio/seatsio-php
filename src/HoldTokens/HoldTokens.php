<?php

namespace Seatsio\HoldTokens;

use GuzzleHttp\Client;
use Seatsio\GuzzleResponseDecoder;
use Seatsio\SeatsioJsonMapper;
use stdClass;

class HoldTokens
{

    /**
     * @var Client
     */
    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function create(?int $expiresInMinutes = null): HoldToken
    {
        $request = new stdClass();
        if (!is_null($expiresInMinutes)) {
            $request->expiresInMinutes = $expiresInMinutes;
        }
        $res = $this->client->post('/hold-tokens', ['json' => $request]);
        $json = GuzzleResponseDecoder::decodeToJson($res);
        $mapper = SeatsioJsonMapper::create();
        return $mapper->map($json, new HoldToken());
    }

    public function expireInMinutes(string $holdToken, int $minutes): HoldToken
    {
        $request = new stdClass();
        $request->expiresInMinutes = $minutes;
        $res = $this->client->post('/hold-tokens/' . $holdToken, ['json' => $request]);
        $json = GuzzleResponseDecoder::decodeToJson($res);
        $mapper = SeatsioJsonMapper::create();
        return $mapper->map($json, new HoldToken());
    }

    public function retrieve(string $holdToken): HoldToken
    {
        $res = $this->client->get('/hold-tokens/' . $holdToken);
        $json = GuzzleResponseDecoder::decodeToJson($res);
        $mapper = SeatsioJsonMapper::create();
        return $mapper->map($json, new HoldToken());
    }

}
