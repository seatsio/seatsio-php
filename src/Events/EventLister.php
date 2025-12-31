<?php

namespace Seatsio\Events;

use Seatsio\PageFetcher;

class EventLister
{

    protected $pageFetcher;

    public function __construct(PageFetcher $pageFetcher)
    {
        $this->pageFetcher = $pageFetcher;
    }

    public function all(): EventPagedIterator
    {
        return new EventPagedIterator($this->pageFetcher);
    }

    public function firstPage(?int $pageSize = null): EventPage
    {
        return $this->pageFetcher->fetchAfter(null, [], $pageSize);
    }

    public function pageAfter(int $afterId, ?int $pageSize = null): EventPage
    {
        return $this->pageFetcher->fetchAfter($afterId, [], $pageSize);
    }

    public function pageBefore(int $beforeId, ?int $pageSize = null): EventPage
    {
        return $this->pageFetcher->fetchBefore($beforeId, [], $pageSize);
    }
}
