<?php

namespace Seatsio\Charts;

use Seatsio\Lister;

class ChartLister extends Lister
{

    /**
     * @param $filter string
     * @return $this
     */
    public function setFilter($filter)
    {
        $this->pageFetcher->setQueryParam('filter', $filter);
        return $this;
    }

    /**
     * @param $tag string
     * @return $this
     */
    public function setTag($tag)
    {
        $this->pageFetcher->setQueryParam('tag', $tag);
        return $this;
    }

    /**
     * @return $this
     */
    public function setExpandEvents()
    {
        $this->pageFetcher->setQueryParam('expand', 'events');
        return $this;
    }

    /**
     * @return ChartPagedIterator
     */
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