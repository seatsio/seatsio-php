<?php

namespace Seatsio;

use GuzzleHttp\Client;

class SeatsioClient
{
    private $client;

    public function __construct($secretKey, $baseUrl = 'https://api.seats.io/')
    {
        $this->client = new Client([
            'base_uri' => $baseUrl,
            'auth' => [$secretKey, null]
        ]);
    }

    public function createChart($name = null, $venueType = null, $categories = null)
    {
        $request = new \stdClass();
        if ($name) {
            $request->name = $name;
        }
        if ($venueType) {
            $request->venueType = $venueType;
        }
        if ($categories) {
            $request->categories = $categories;
        }
        $res = $this->client->request('POST', '/charts', ['json' => $request]);
        return \GuzzleHttp\json_decode($res->getBody())->key;
    }

    public function retrieveChart($chartKey)
    {
        $res = $this->client->request('GET', '/charts/' . $chartKey . '/version/published');
        return \GuzzleHttp\json_decode($res->getBody());
    }

    public function listCharts($limit = null)
    {
        return new PagedIterator('/charts', $limit, $this->client);
    }
}