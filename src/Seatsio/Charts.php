<?php

namespace Seatsio;

use JsonMapper;

class Charts
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
     * @return Chart
     */
    public function create($name = null, $venueType = null, $categories = null)
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
        $json = \GuzzleHttp\json_decode($res->getBody());
        $mapper = new JsonMapper();
        return $mapper->map($json, new Chart());
    }

    public function retrieve($chartKey)
    {
        $res = $this->client->request('GET', '/charts/' . $chartKey . '/version/published');
        return \GuzzleHttp\json_decode($res->getBody());
    }

    /**
     * @return Lister
     */
    public function lister()
    {
        return new Lister(new PageFetcher('/charts', $this->client, $this->pageSize, function () {
            return new ChartPage();
        }));
    }

}