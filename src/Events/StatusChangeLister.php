<?php

namespace Seatsio\Events;

use Seatsio\PageFetcher;

class StatusChangeLister
{

    protected $pageFetcher;

    public function __construct(PageFetcher $pageFetcher)
    {
        $this->pageFetcher = $pageFetcher;
    }

    public function all(): StatusChangePagedIterator
    {
        return new StatusChangePagedIterator($this->pageFetcher);
    }

    public function firstPage(int $pageSize = null): StatusChangePage
    {
        return $this->pageFetcher->fetchAfter(null, [], $pageSize);
    }

    public function pageAfter(int $afterId, int $pageSize = null): StatusChangePage
    {
        return $this->pageFetcher->fetchAfter($afterId, [], $pageSize);
    }

    public function pageBefore(int $beforeId, int $pageSize = null): StatusChangePage
    {
        return $this->pageFetcher->fetchBefore($beforeId, [], $pageSize);
    }
}
