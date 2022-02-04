<?php

namespace Seatsio\Subaccounts;

use Seatsio\PageFetcher;

class FilterableSubaccountLister
{

    protected $pageFetcher;

    public function __construct(PageFetcher $pageFetcher)
    {
        $this->pageFetcher = $pageFetcher;
    }

    /**
     * @param $queryParams SubaccountListParams[]
     */
    public function all(array $queryParams): SubaccountPagedIterator
    {
        return new SubaccountPagedIterator($this->pageFetcher, $queryParams);
    }

    /**
     * @param $queryParams SubaccountListParams[]
     */
    public function firstPage(array $queryParams, int $pageSize = null): SubaccountPage
    {
        return $this->pageFetcher->fetchAfter(null, $queryParams, $pageSize);
    }

    /**
     * @param $queryParams SubaccountListParams[]
     */
    public function pageAfter(int $afterId, array $queryParams, int $pageSize = null): SubaccountPage
    {
        return $this->pageFetcher->fetchAfter($afterId, $queryParams, $pageSize);
    }

    /**
     * @param $queryParams SubaccountListParams[]
     */
    public function pageBefore(int $beforeId, array $queryParams, int $pageSize = null): SubaccountPage
    {
        return $this->pageFetcher->fetchBefore($beforeId, $queryParams, $pageSize);
    }
}
