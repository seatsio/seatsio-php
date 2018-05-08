<?php

namespace Seatsio\Events;

class StatusChangeLister
{

    protected $pageFetcher;

    public function __construct($pageFetcher)
    {
        $this->pageFetcher = $pageFetcher;
    }

    /**
     * @return StatusChangePagedIterator
     */
    public function all()
    {
        return new StatusChangePagedIterator($this->pageFetcher);
    }

    /**
     * @param $pageSize int
     * @return StatusChangePage
     */
    public function firstPage($pageSize = null)
    {
        return $this->pageFetcher->fetchAfter(null, [], $pageSize);
    }

    /**
     * @param $afterId int
     * @param $pageSize int
     * @return StatusChangePage
     */
    public function pageAfter($afterId, $pageSize = null)
    {
        return $this->pageFetcher->fetchAfter($afterId, [], $pageSize);
    }

    /**
     * @param $beforeId int
     * @param $pageSize int
     * @return StatusChangePage
     */
    public function pageBefore($beforeId, $pageSize = null)
    {
        return $this->pageFetcher->fetchBefore($beforeId, [], $pageSize);
    }
}