<?php

namespace Seatsio\Charts;

class FilterableChartLister
{

    protected $pageFetcher;

    public function __construct($pageFetcher)
    {
        $this->pageFetcher = $pageFetcher;
    }

    /**
     * @return ChartPagedIterator
     */
    public function all($queryParams)
    {
        return new ChartPagedIterator($this->pageFetcher, $queryParams);
    }

    /**
     * @return ChartPage
     */
    public function firstPage($queryParams, $pageSize = null)
    {
        return $this->pageFetcher->fetchAfter(null, $queryParams, $pageSize);
    }

    /**
     * @return ChartPage
     */
    public function pageAfter($afterId, $queryParams, $pageSize = null)
    {
        return $this->pageFetcher->fetchAfter($afterId, $queryParams, $pageSize);
    }

    /**
     * @return ChartPage
     */
    public function pageBefore($beforeId, $queryParams, $pageSize = null)
    {
        return $this->pageFetcher->fetchBefore($beforeId, $queryParams, $pageSize);
    }

}