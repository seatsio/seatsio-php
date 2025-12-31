<?php

namespace Seatsio\TicketBuyers;

use Seatsio\PageFetcher;

class TicketBuyerIdLister
{

    protected $pageFetcher;

    public function __construct(PageFetcher $pageFetcher)
    {
        $this->pageFetcher = $pageFetcher;
    }

    public function all(): TicketBuyerIdPagedIterator
    {
        return new TicketBuyerIdPagedIterator($this->pageFetcher);
    }

    public function firstPage(?int $pageSize = null): TicketBuyerIdPage
    {
        return $this->pageFetcher->fetchAfter(null, [], $pageSize);
    }

    public function pageAfter(int $afterId, ?int $pageSize = null): TicketBuyerIdPage
    {
        return $this->pageFetcher->fetchAfter($afterId, [], $pageSize);
    }

    public function pageBefore(int $beforeId, ?int $pageSize = null): TicketBuyerIdPage
    {
        return $this->pageFetcher->fetchBefore($beforeId, [], $pageSize);
    }
}
