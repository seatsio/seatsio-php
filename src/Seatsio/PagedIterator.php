<?php

namespace Seatsio;

use Iterator;

class PagedIterator implements Iterator
{

    private $currentPage;
    private $indexInCurrentPage;
    private $pageFetcher;

    public function __construct($pageFetcher)
    {
        $this->pageFetcher = $pageFetcher;
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

    public function valid()
    {
        $currentPage = $this->getCurrentPage();
        return $this->indexInCurrentPage < count($currentPage->items);
    }

    public function rewind()
    {
        $this->currentPage = null;
        $this->indexInCurrentPage = 0;
    }

    private function getCurrentPage()
    {
        if (!$this->currentPage) {
            $this->currentPage = $this->pageFetcher->fetch(null);
        } else if ($this->nextPageMustBeFetched()) {
            $this->currentPage = $this->pageFetcher->fetch($this->currentPage->next_page_starts_after);
            $this->indexInCurrentPage = 0;
        }
        return $this->currentPage;
    }

    private function nextPageMustBeFetched()
    {
        return $this->indexInCurrentPage >= count($this->currentPage->items) &&
            isset($this->currentPage->next_page_starts_after);
    }

}