<?php

namespace Seatsio\Subaccounts;

use Seatsio\PageFetcher;

class SubaccountLister
{

    protected $pageFetcher;

    public function __construct(PageFetcher $pageFetcher)
    {
        $this->pageFetcher = $pageFetcher;
    }

    public function all(): SubaccountPagedIterator
    {
        return new SubaccountPagedIterator($this->pageFetcher);
    }

    /**
     * @return SubaccountPage
     */
    public function firstPage(int $pageSize = null): SubaccountPage
    {
        return $this->pageFetcher->fetchAfter(null, [], $pageSize);
    }

    public function pageAfter(int $afterId, int $pageSize = null): SubaccountPage
    {
        return $this->pageFetcher->fetchAfter($afterId, [], $pageSize);
    }

    public function pageBefore(int $beforeId, int $pageSize = null): SubaccountPage
    {
        return $this->pageFetcher->fetchBefore($beforeId, [], $pageSize);
    }
}
