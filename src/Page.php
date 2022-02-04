<?php

namespace Seatsio;


class Page
{
    public $nextPageStartsAfter;
    public $previousPageEndsBefore;

    public function setNextPageStartsAfter($nextPageStartsAfter)
    {
        $this->nextPageStartsAfter = $nextPageStartsAfter;
    }

    public function setPreviousPageEndsBefore($previousPageEndsBefore)
    {
        $this->previousPageEndsBefore = $previousPageEndsBefore;
    }
}
