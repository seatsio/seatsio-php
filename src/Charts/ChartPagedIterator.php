<?php

namespace Seatsio\Charts;

use Seatsio\PagedIterator;

class ChartPagedIterator extends PagedIterator
{

    /**
     * @return Chart
     */
    public function current()
    {
        return parent::current();
    }

}