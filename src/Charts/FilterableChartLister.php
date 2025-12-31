<?php

namespace Seatsio\Charts;

use Seatsio\PageFetcher;

class FilterableChartLister
{

    protected $pageFetcher;

    public function __construct(PageFetcher $pageFetcher)
    {
        $this->pageFetcher = $pageFetcher;
    }

    public function all(array $queryParams): ChartPagedIterator
    {
        return new ChartPagedIterator($this->pageFetcher, $queryParams);
    }

    public function firstPage(array $queryParams, ?int $pageSize = null): ChartPage
    {
        return $this->pageFetcher->fetchAfter(null, $queryParams, $pageSize);
    }

    public function pageAfter(int $afterId, array $queryParams, ?int $pageSize = null): ChartPage
    {
        return $this->pageFetcher->fetchAfter($afterId, $queryParams, $pageSize);
    }

    public function pageBefore(int $beforeId, array $queryParams, ?int $pageSize = null): ChartPage
    {
        return $this->pageFetcher->fetchBefore($beforeId, $queryParams, $pageSize);
    }

}
