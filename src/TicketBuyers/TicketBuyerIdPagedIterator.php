<?php

namespace Seatsio\TicketBuyers;

use Seatsio\PagedIterator;

class TicketBuyerIdPagedIterator extends PagedIterator
{

    public function current(): string
    {
        return parent::current();
    }

    public function key(): int
    {
        return $this->current();
    }

}
