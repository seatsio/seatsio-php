<?php

namespace Seatsio\Charts;

use JsonMapper;
use Seatsio\Events\EventLister;
use Seatsio\Events\EventPage;
use Seatsio\PageFetcher;

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

    public function update($key, $name = null, $categories = null)
    {
        $request = new \stdClass();
        if ($name) {
            $request->name = $name;
        }
        if ($categories) {
            $request->categories = $categories;
        }
        $this->client->request('POST', '/charts/' . $key, ['json' => $request]);
    }

    public function retrieve($chartKey)
    {
        $res = $this->client->request('GET', '/charts/' . $chartKey . '/version/published');
        return \GuzzleHttp\json_decode($res->getBody());
    }

    /**
     * @return ChartLister
     */
    public function lister()
    {
        return new ChartLister(new PageFetcher('/charts', $this->client, $this->pageSize, function () {
            return new ChartPage();
        }));
    }

    /**
     * @return EventLister
     */
    public function events($chartKey)
    {
        return new EventLister(new PageFetcher('/charts/' . $chartKey . '/events', $this->client, $this->pageSize, function () {
            return new EventPage();
        }));
    }

}