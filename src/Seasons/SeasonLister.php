<?php

namespace Seatsio\Seasons;

use Seatsio\PageFetcher;

class SeasonLister
{

    protected $pageFetcher;

    public function __construct(PageFetcher $pageFetcher)
    {
        $this->pageFetcher = $pageFetcher;
    }

    public function all(): SeasonPagedIterator
    {
        return new SeasonPagedIterator($this->pageFetcher);
    }

    public function firstPage(int $pageSize = null): SeasonPage
    {
        return $this->pageFetcher->fetchAfter(null, [], $pageSize);
    }

    public function pageAfter(int $afterId, int $pageSize = null): SeasonPage
    {
        return $this->pageFetcher->fetchAfter($afterId, [], $pageSize);
    }

    public function pageBefore(int $beforeId, int $pageSize = null): SeasonPage
    {
        return $this->pageFetcher->fetchBefore($beforeId, [], $pageSize);
    }
}
