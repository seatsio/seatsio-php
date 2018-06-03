<?php

namespace Seatsio\Subaccounts;

use Seatsio\SeatsioClientTest;

class ListInactiveSubaccountsTest extends SeatsioClientTest
{

    public function test()
    {
        $subaccount1 = $this->seatsioClient->subaccounts->create();
        $this->seatsioClient->subaccounts->deactivate($subaccount1->id);

        $subaccount2 = $this->seatsioClient->subaccounts->create();
        $this->seatsioClient->subaccounts->deactivate($subaccount2->id);

        $this->seatsioClient->subaccounts->create();

        $subaccounts = $this->seatsioClient->subaccounts->inactive->all();
        $subaccountIds = \Functional\map($subaccounts, function($subaccount) { return $subaccount->id; });

        self::assertEquals([$subaccount2->id, $subaccount1->id], array_values($subaccountIds));
    }

}