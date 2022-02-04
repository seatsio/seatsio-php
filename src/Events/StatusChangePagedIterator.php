<?php

namespace Seatsio\Events;

use Seatsio\PagedIterator;

class StatusChangePagedIterator extends PagedIterator
{

    public function current(): StatusChange
    {
        return parent::current();
    }

}
