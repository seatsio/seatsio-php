<?php

namespace Seatsio\Events;

use Seatsio\PagedIterator;

class EventPagedIterator extends PagedIterator
{

    /**
     * @return Event
     */
    public function current()
    {
        return parent::current();
    }

}