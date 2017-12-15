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
        return \GuzzleHttp\json_decode($res->getBody());
    }

    public function retrieveChart($chartKey)
    {
        $res = $this->client->request('GET', '/charts/' . $chartKey . '/version/published');
        return \GuzzleHttp\json_decode($res->getBody());
    }

    public function listAllCharts()
    {
        return new PagedIterator(new PageFetcher('/charts', $this->client, $this->pageSize));
    }

    public function listCharts($afterId = null)
    {
        $pageFetcher = new PageFetcher('/charts', $this->client, $this->pageSize);
        return $pageFetcher->fetchAfter($afterId);
    }

    public function listChartsBefore($beforeId)
    {
        $pageFetcher = new PageFetcher('/charts', $this->client, $this->pageSize);
        return $pageFetcher->fetchBefore($beforeId);
    }

}