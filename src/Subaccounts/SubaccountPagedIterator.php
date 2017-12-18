<?php

namespace Seatsio\Subaccounts;

use Seatsio\PagedIterator;
use Seatsio\Subaccounts\Subaccount;

class SubaccountPagedIterator extends PagedIterator
{

    /**
     * @return Subaccount
     */
    public function current()
    {
        return parent::current();
    }

}