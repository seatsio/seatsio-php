<?php

namespace Seatsio\Seasons;

use Seatsio\PagedIterator;

class SeasonPagedIterator extends PagedIterator
{

    /**
     * @return Season
     */
    public function current()
    {
        return parent::current();
    }

}
