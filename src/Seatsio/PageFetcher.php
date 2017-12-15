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

    public function fetch($afterId)
    {
        $query = [];
        if ($this->pageSize) {
            $query['limit'] = $this->pageSize;
        }
        if ($afterId) {
            $query['start_after_id'] = $afterId;
        }
        $res = $this->client->request('GET', $this->url, ['query' => $query]);
        return \GuzzleHttp\json_decode($res->getBody());
    }
}