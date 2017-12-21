<?php

namespace Seatsio\Events;

use Seatsio\Lister;

class StatusChangeLister extends Lister
{

    public function all()
    {
        return new StatusChangePagedIterator($this->pageFetcher);
    }

    /**
     * @return StatusChangePage
     */
    public function firstPage()
    {
        return parent::firstPage();
    }

    /**
     * @return StatusChangePage
     */
    public function pageAfter($afterId)
    {
        return parent::pageAfter($afterId);
    }

    /**
     * @return StatusChangePage
     */
    public function pageBefore($beforeId)
    {
        return parent::pageBefore($beforeId);
    }
}