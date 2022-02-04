<?php

namespace Seatsio\Charts;

use Seatsio\PagedIterator;

class ChartPagedIterator extends PagedIterator
{

    public function current(): Chart
    {
        return parent::current();
    }

}
