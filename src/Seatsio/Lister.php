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
     * @return Page
     */
    public function firstPage()
    {
        return $this->pageFetcher->fetchAfter(null);
    }

    /**
     * @return Page
     */
    public function pageAfter($afterId)
    {
        return $this->pageFetcher->fetchAfter($afterId);
    }

    /**
     * @return Page
     */
    public function pageBefore($beforeId)
    {
        return $this->pageFetcher->fetchBefore($beforeId);
    }
}