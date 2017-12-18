<?php

namespace Seatsio\Events;

use Seatsio\Lister;

class EventLister extends Lister
{

    public function all()
    {
        return new EventPagedIterator($this->pageFetcher);
    }

    /**
     * @return EventPage
     */
    public function firstPage()
    {
        return parent::firstPage();
    }

    /**
     * @return EventPage
     */
    public function pageAfter($afterId)
    {
        return parent::pageAfter($afterId);
    }

    /**
     * @return EventPage
     */
    public function pageBefore($beforeId)
    {
        return parent::pageBefore($beforeId);
    }
}