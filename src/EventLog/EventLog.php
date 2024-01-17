<?php

namespace Seatsio\EventLog;

use GuzzleHttp\Client;
use Seatsio\PageFetcher;

class EventLog
{

    /**
     * @var Client
     */
    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function listAll(): EventLogItemPagedIterator
    {
        return $this->iterator()->all();
    }

    public function listFirstPage(int $pageSize = null): EventLogItemPage
    {
        return $this->iterator()->firstPage($pageSize);
    }

    public function listPageAfter(int $afterId, int $pageSize = null): EventLogItemPage
    {
        return $this->iterator()->pageAfter($afterId, $pageSize);
    }

    public function listPageBefore(int $beforeId, int $pageSize = null): EventLogItemPage
    {
        return $this->iterator()->pageBefore($beforeId, $pageSize);
    }

    private function iterator(): EventLogItemLister
    {
        return new EventLogItemLister(new PageFetcher('/event-log', $this->client, function () {
            return new EventLogItemPage();
        }));
    }
}
