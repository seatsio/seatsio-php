<?php

namespace Seatsio\Subaccounts;

use Seatsio\Lister;

class SubaccountLister extends Lister
{

    public function all()
    {
        return new SubaccountPagedIterator($this->pageFetcher);
    }

    /**
     * @return SubaccountPage
     */
    public function firstPage()
    {
        return parent::firstPage();
    }

    /**
     * @return SubaccountPage
     */
    public function pageAfter($afterId)
    {
        return parent::pageAfter($afterId);
    }

    /**
     * @return SubaccountPage
     */
    public function pageBefore($beforeId)
    {
        return parent::pageBefore($beforeId);
    }
}