<?php

namespace Seatsio\Events;

use Seatsio\PagedIterator;

class StatusChangePagedIterator extends PagedIterator
{

    /**
     * @return StatusChange
     */
    public function current()
    {
        return parent::current();
    }

}