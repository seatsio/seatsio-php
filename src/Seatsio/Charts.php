<?php

namespace Seatsio;

class Charts {

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
        return \GuzzleHttp\json_decode($res->getBody());
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
        return new Lister(new PageFetcher('/charts', $this->client, $this->pageSize));
    }

}