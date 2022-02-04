<?php

namespace Seatsio\Subaccounts;

use Seatsio\PagedIterator;
use Seatsio\Subaccounts\Subaccount;

class SubaccountPagedIterator extends PagedIterator
{

    public function current(): Subaccount
    {
        return parent::current();
    }

}
