<?php

namespace Seatsio;

use Iterator;

class PagedIterator implements Iterator
{

    /**
     * @var Page
     */
    private $currentPage;
    private $indexInCurrentPage = 0;
    private $params;

    /**
     * @var PageFetcher
     */
    private $pageFetcher;

    public function __construct(PageFetcher $pageFetcher, array $params = [])
    {
        $this->pageFetcher = $pageFetcher;
        $this->params = $params;
    }

    public function current()
    {
        return $this->getCurrentPage()->items[$this->indexInCurrentPage];
    }

    public function next()
    {
        $this->indexInCurrentPage++;
    }

    public function key()
    {
        return $this->current()->id;
    }

    public function valid(): bool
    {
        $currentPage = $this->getCurrentPage();
        return $this->indexInCurrentPage < count($currentPage->items);
    }

    public function rewind(): void
    {
        $this->currentPage = null;
        $this->indexInCurrentPage = 0;
    }

    private function getCurrentPage()
    {
        if (!$this->currentPage) {
            $this->currentPage = $this->pageFetcher->fetchAfter(null, $this->params, null);
        } else if ($this->nextPageMustBeFetched()) {
            $this->currentPage = $this->pageFetcher->fetchAfter($this->currentPage->nextPageStartsAfter, $this->params, null);
            $this->indexInCurrentPage = 0;
        }
        return $this->currentPage;
    }

    private function nextPageMustBeFetched(): bool
    {
        return $this->indexInCurrentPage >= count($this->currentPage->items) &&
            isset($this->currentPage->nextPageStartsAfter);
    }

}
