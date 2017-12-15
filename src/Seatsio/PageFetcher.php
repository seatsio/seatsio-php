<?php

namespace Seatsio;


class PageFetcher
{
    private $url;

    /**
     * @var \GuzzleHttp\Client
     */
    private $client;

    private $pageSize;

    public function __construct($url, $client, $pageSize)
    {
        $this->url = $url;
        $this->client = $client;
        $this->pageSize = $pageSize;
    }

    public function fetchAfter($afterId = null)
    {
        $query = [];
        if ($afterId) {
            $query['start_after_id'] = $afterId;
        }
        return $this->fetch($query);
    }

    public function fetchBefore($beforeId = null)
    {
        $query = [];
        if ($beforeId) {
            $query['end_before_id'] = $beforeId;
        }
        return $this->fetch($query);
    }

    public function fetch($query)
    {
        if ($this->pageSize) {
            $query['limit'] = $this->pageSize;
        }
        $res = $this->client->request('GET', $this->url, ['query' => $query]);
        return \GuzzleHttp\json_decode($res->getBody());
    }
}