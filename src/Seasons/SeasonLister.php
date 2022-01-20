<?php

namespace Seatsio\Seasons;

class SeasonLister
{

    protected $pageFetcher;

    public function __construct($pageFetcher)
    {
        $this->pageFetcher = $pageFetcher;
    }

    /**
     * @return SeasonPagedIterator
     */
    public function all()
    {
        return new SeasonPagedIterator($this->pageFetcher);
    }

    /**
     * @param $pageSize int
     * @return SeasonPage
     */
    public function firstPage($pageSize = null)
    {
        return $this->pageFetcher->fetchAfter(null, [], $pageSize);
    }

    /**
     * @param $afterId int
     * @param $pageSize int
     * @return SeasonPage
     */
    public function pageAfter($afterId, $pageSize = null)
    {
        return $this->pageFetcher->fetchAfter($afterId, [], $pageSize);
    }

    /**
     * @param $beforeId int
     * @param $pageSize int
     * @return SeasonPage
     */
    public function pageBefore($beforeId, $pageSize = null)
    {
        return $this->pageFetcher->fetchBefore($beforeId, [], $pageSize);
    }
}
