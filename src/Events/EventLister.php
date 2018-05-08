<?php

namespace Seatsio\Events;

class EventLister
{

    protected $pageFetcher;

    public function __construct($pageFetcher)
    {
        $this->pageFetcher = $pageFetcher;
    }

    /**
     * @return EventPagedIterator
     */
    public function all()
    {
        return new EventPagedIterator($this->pageFetcher);
    }

    /**
     * @param $pageSize int
     * @return EventPage
     */
    public function firstPage($pageSize = null)
    {
        return $this->pageFetcher->fetchAfter(null, [], $pageSize);
    }

    /**
     * @param $afterId int
     * @param $pageSize int
     * @return EventPage
     */
    public function pageAfter($afterId, $pageSize = null)
    {
        return $this->pageFetcher->fetchAfter($afterId, [], $pageSize);
    }

    /**
     * @param $beforeId int
     * @param $pageSize int
     * @return EventPage
     */
    public function pageBefore($beforeId, $pageSize = null)
    {
        return $this->pageFetcher->fetchBefore($beforeId, [], $pageSize);
    }
}