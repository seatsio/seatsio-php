<?php

namespace Seatsio\Subaccounts;

use Seatsio\SeatsioClientTest;

class ListAllSubaccountsTest extends SeatsioClientTest
{

    public function test()
    {
        $subaccount1 = $this->seatsioClient->subaccounts->create();
        $subaccount2 = $this->seatsioClient->subaccounts->create();
        $subaccount3 = $this->seatsioClient->subaccounts->create();

        $subaccounts = $this->seatsioClient->subaccounts->listAll();
        $subaccountIds = \Functional\map($subaccounts, function($subaccount) { return $subaccount->id; });

        self::assertEquals([$subaccount3->id, $subaccount2->id, $subaccount1->id], array_values($subaccountIds));
    }

    public function testWithFilter()
    {
        $this->seatsioClient->subaccounts->create();
        $subaccount2 = $this->seatsioClient->subaccounts->create("test-/@/");
        $this->seatsioClient->subaccounts->create();

        $subaccounts = $this->seatsioClient->subaccounts->listAll((new SubaccountListParams())->withFilter('test'));
        $subaccountIds = \Functional\map($subaccounts, function($subaccount) { return $subaccount->id; });

        self::assertEquals([$subaccount2->id], array_values($subaccountIds));
    }

}