<?php

namespace Seatsio\EventLog;

use Seatsio\PageFetcher;

class EventLogItemLister
{

    protected $pageFetcher;

    public function __construct(PageFetcher $pageFetcher)
    {
        $this->pageFetcher = $pageFetcher;
    }

    public function all(): EventLogItemPagedIterator
    {
        return new EventLogItemPagedIterator($this->pageFetcher);
    }

    public function firstPage(int $pageSize = null): EventLogItemPage
    {
        return $this->pageFetcher->fetchAfter(null, [], $pageSize);
    }

    public function pageAfter(int $afterId, int $pageSize = null): EventLogItemPage
    {
        return $this->pageFetcher->fetchAfter($afterId, [], $pageSize);
    }

    public function pageBefore(int $beforeId, int $pageSize = null): EventLogItemPage
    {
        return $this->pageFetcher->fetchBefore($beforeId, [], $pageSize);
    }
}
