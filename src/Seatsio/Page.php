<?php

namespace Seatsio;


class Page
{
    public $items;
    public $nextPageStartsAfter;
    public $previousPageEndsBefore;

    public function __construct($json)
    {
        $this->items = $json->items;
        $this->nextPageStartsAfter = isset($json->next_page_starts_after) ? $json->next_page_starts_after : null;
        $this->previousPageEndsBefore = isset($json->previous_page_ends_before) ? $json->previous_page_ends_before : null;
    }
}