<?php

namespace Seatsio;

class Lister
{
    /**
     * @var PageFetcher
     */
    private $pageFetcher;

    public function __construct($pageFetcher)
    {
        $this->pageFetcher = $pageFetcher;
    }

    public function all()
    {
        return new PagedIterator($this->pageFetcher);
    }

    /**
     * @return ChartPage
     */
    public function firstPage()
    {
        return $this->pageFetcher->fetchAfter(null);
    }

    /**
     * @return ChartPage
     */
    public function pageAfter($afterId)
    {
        return $this->pageFetcher->fetchAfter($afterId);
    }

    /**
     * @return ChartPage
     */
    public function pageBefore($beforeId)
    {
        return $this->pageFetcher->fetchBefore($beforeId);
    }
}