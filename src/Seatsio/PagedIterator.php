<?php

namespace Seatsio;

use GuzzleHttp\Client;
use Iterator;

class PagedIterator implements Iterator
{

    private $url;
    private $limit;
    private $client;
    private $currentPage;
    private $nextPageStartsAfter;

    public function __construct($url, $limit, Client $client)
    {
        $this->url = $url;
        $this->limit = $limit;
        $this->client = $client;
    }

    public function current()
    {
        if (!$this->currentPage) {
            $query = [];
            if ($this->limit) {
                $query['limit'] = $this->limit;
            }
            if ($this->nextPageStartsAfter) {
                $query['start_after_id'] = $this->nextPageStartsAfter;
            }
            $res = $this->client->request('GET', $this->url, ['query' => $query]);
            $this->currentPage = \GuzzleHttp\json_decode($res->getBody());
            $this->nextPageStartsAfter = self::determineNextPageStartsAfter($this->currentPage);
        }
        return $this->currentPage->items;
    }

    public function next()
    {
        $this->currentPage = null;
    }

    public function key()
    {
    }

    public function valid()
    {
        return $this->nextPageStartsAfter !== -1;
    }

    public function rewind()
    {
        $this->currentPage = null;
        $this->nextPageStartsAfter = null;
    }

    private static function determineNextPageStartsAfter($page)
    {
        if (isset($page->next_page_starts_after)) {
            return $page->next_page_starts_after;
        }
        return -1;
    }
}