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

    /**
     * @param $pageSize int
     * @return $this
     */
    public function setPageSize($pageSize)
    {
        $this->pageFetcher->setPageSize($pageSize);
        return $this;
    }
}