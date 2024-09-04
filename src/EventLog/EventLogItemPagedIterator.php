<?php

namespace Seatsio\EventLog;

use Seatsio\PagedIterator;

class EventLogItemPagedIterator extends PagedIterator
{

    public function current(): EventLogItem
    {
        return parent::current();
    }

}
