<?php

namespace Seatsio\Charts;

use Seatsio\Lister;

class ChartLister extends Lister
{

    public function all()
    {
        return new ChartPagedIterator($this->pageFetcher);
    }

    /**
     * @return ChartPage
     */
    public function firstPage()
    {
        return parent::firstPage();
    }

    /**
     * @return ChartPage
     */
    public function pageAfter($afterId)
    {
        return parent::pageAfter($afterId);
    }

    /**
     * @return ChartPage
     */
    public function pageBefore($beforeId)
    {
        return parent::pageBefore($beforeId);
    }
}