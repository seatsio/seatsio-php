<?php

namespace Seatsio;

use GuzzleHttp\Client;
use Iterator;

class PagedIterator implements Iterator
{

    private $url;
    private $pageSize;
    private $client;
    private $currentPage;
    private $indexInCurrentPage;

    public function __construct($url, $pageSize, Client $client)
    {
        $this->url = $url;
        $this->pageSize = $pageSize;
        $this->client = $client;
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
            $this->currentPage = $this::fetchPage(null);
        } else if ($this->nextPageMustBeFetched()) {
            $this->currentPage = $this::fetchPage($this->currentPage->next_page_starts_after);
            $this->indexInCurrentPage = 0;
        }
        return $this->currentPage;
    }

    private function fetchPage($startAfterId)
    {
        $query = [];
        if ($this->pageSize) {
            $query['limit'] = $this->pageSize;
        }
        if ($startAfterId) {
            $query['start_after_id'] = $startAfterId;
        }
        $res = $this->client->request('GET', $this->url, ['query' => $query]);
        return \GuzzleHttp\json_decode($res->getBody());
    }

    private function nextPageMustBeFetched()
    {
        return $this->indexInCurrentPage >= count($this->currentPage->items) &&
            isset($this->currentPage->next_page_starts_after);
    }

}