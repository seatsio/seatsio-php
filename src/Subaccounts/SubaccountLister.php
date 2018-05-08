<?php

namespace Seatsio\Subaccounts;

class SubaccountLister
{

    protected $pageFetcher;

    public function __construct($pageFetcher)
    {
        $this->pageFetcher = $pageFetcher;
    }

    /**
     * @return SubaccountPagedIterator
     */
    public function all()
    {
        return new SubaccountPagedIterator($this->pageFetcher);
    }

    /**
     * @param $pageSize int
     * @return SubaccountPage
     */
    public function firstPage($pageSize = null)
    {
        return $this->pageFetcher->fetchAfter(null, [], $pageSize);
    }

    /**
     * @param $afterId int
     * @param $pageSize int
     * @return SubaccountPage
     */
    public function pageAfter($afterId, $pageSize = null)
    {
        return $this->pageFetcher->fetchAfter($afterId, [], $pageSize);
    }

    /**
     * @param $beforeId int
     * @param $pageSize int
     * @return SubaccountPage
     */
    public function pageBefore($beforeId, $pageSize = null)
    {
        return $this->pageFetcher->fetchBefore($beforeId, [], $pageSize);
    }
}