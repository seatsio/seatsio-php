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
    private $pageCreator;
    private $queryParams = [];

    public function __construct($url, $client, $pageCreator)
    {
        $this->url = $url;
        $this->client = $client;
        $this->pageCreator = $pageCreator;
    }

    public function fetchAfter($afterId = null)
    {
        $query = [];
        if ($afterId !== null) {
            $query['start_after_id'] = $afterId;
        }
        return $this->fetch($query);
    }

    public function fetchBefore($beforeId = null)
    {
        $query = [];
        if ($beforeId !== null) {
            $query['end_before_id'] = $beforeId;
        }
        return $this->fetch($query);
    }

    public function fetch($query)
    {
        if ($this->pageSize) {
            $query['limit'] = $this->pageSize;
        }
        $res = $this->client->get($this->url, ['query' => array_merge($query, $this->queryParams)]);
        $json = \GuzzleHttp\json_decode($res->getBody());
        $mapper = SeatsioJsonMapper::create();
        return $mapper->map($json, $this->pageCreator->__invoke());
    }

    /**
     * @param $name string
     * @param $value mixed
     */
    public function setQueryParam($name, $value)
    {
        $this->queryParams[$name] = $value;
    }

    /**
     * @param $pageSize int
     */
    public function setPageSize($pageSize)
    {
        $this->pageSize = $pageSize;
    }
}