<?php

namespace Seatsio\Charts;

use Seatsio\PageFetcher;

class ChartLister
{

    protected $pageFetcher;

    public function __construct(PageFetcher $pageFetcher)
    {
        $this->pageFetcher = $pageFetcher;
    }

    public function all(): ChartPagedIterator
    {
        return new ChartPagedIterator($this->pageFetcher);
    }

    public function firstPage(int $pageSize = null): ChartPage
    {
        return $this->pageFetcher->fetchAfter(null, [], $pageSize);
    }

    public function pageAfter(int $afterId, int $pageSize = null): ChartPage
    {
        return $this->pageFetcher->fetchAfter($afterId, [], $pageSize);
    }

    public function pageBefore(int $beforeId, int $pageSize = null): ChartPage
    {
        return $this->pageFetcher->fetchBefore($beforeId, [], $pageSize);
    }

}
