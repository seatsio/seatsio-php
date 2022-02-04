<?php

namespace Seatsio\Seasons;

use Seatsio\PagedIterator;

class SeasonPagedIterator extends PagedIterator
{

    public function current(): Season
    {
        return parent::current();
    }

}
