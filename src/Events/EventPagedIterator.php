<?php

namespace Seatsio\Events;

use Seatsio\PagedIterator;

class EventPagedIterator extends PagedIterator
{

    public function current(): Event
    {
        return parent::current();
    }

}
