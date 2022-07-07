<?php

namespace Seatsio;

use GuzzleHttp\Client;
use GuzzleHttp\Utils;

class PageFetcher
{
    private $url;
    /**
     * @var Client
     */
    private $client;
    private $pageCreator;
    private $queryParams;

    public function __construct($url, $client, $pageCreator, $queryParams = null)
    {
        $this->url = $url;
        $this->client = $client;
        $this->pageCreator = $pageCreator;
        $this->queryParams = $queryParams;
    }

    public function fetchAfter($afterId, $queryParams, $pageSize)
    {
        if ($afterId !== null) {
            $queryParams['start_after_id'] = $afterId;
        }
        return $this->fetch($queryParams, $pageSize);
    }

    public function fetchBefore($beforeId, $queryParams, $pageSize)
    {
        if ($beforeId !== null) {
            $queryParams['end_before_id'] = $beforeId;
        }
        return $this->fetch($queryParams, $pageSize);
    }

    public function fetch($queryParams, $pageSize)
    {
        if ($pageSize) {
            $queryParams['limit'] = $pageSize;
        }
        $mergedQueryParams = $this->queryParams ? array_merge($queryParams, $this->queryParams) : $queryParams;
        $res = $this->client->get($this->url, ['query' => $mergedQueryParams]);
        $json = Utils::jsonDecode($res->getBody());
        $mapper = SeatsioJsonMapper::create();
        return $mapper->map($json, $this->pageCreator->__invoke());
    }

}
