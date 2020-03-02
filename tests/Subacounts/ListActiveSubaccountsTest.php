<?php

namespace Seatsio\Subaccounts;

use Seatsio\SeatsioClientTest;

class ListActiveSubaccountsTest extends SeatsioClientTest
{

    public function test()
    {
        $subaccount1 = $this->seatsioClient->subaccounts->create();
        $subaccount2 = $this->seatsioClient->subaccounts->create();
        $subaccount3 = $this->seatsioClient->subaccounts->create();
        $this->seatsioClient->subaccounts->deactivate($subaccount3->id);

        $subaccounts = $this->seatsioClient->subaccounts->active->all();
        $subaccountIds = \Functional\map($subaccounts, function($subaccount) { return $subaccount->id; });

        self::assertEquals([$subaccount2->id, $subaccount1->id, $this->subaccount->id], array_values($subaccountIds));
    }

}
