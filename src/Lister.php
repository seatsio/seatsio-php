<?php

namespace Seatsio;

class Lister
{
    /**
     * @var PageFetcher
     */
    protected $pageFetcher;

    public function __construct($pageFetcher)
    {
        $this->pageFetcher = $pageFetcher;
    }

    public function firstPage()
    {
        return $this->pageFetcher->fetchAfter(null);
    }

    public function pageAfter($afterId)
    {
        return $this->pageFetcher->fetchAfter($afterId);
    }

    public function pageBefore($beforeId)
    {
        return $this->pageFetcher->fetchBefore($beforeId);
    }
}