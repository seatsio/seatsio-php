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

        self::assertEquals([$subaccount3->id, $subaccount2->id, $subaccount1->id, $this->user->mainWorkspace->primaryUser->id], array_values($subaccountIds));
    }

    public function testWithFilter()
    {
        $this->seatsioClient->subaccounts->create();
        $subaccount2 = $this->seatsioClient->subaccounts->create("subaccount2");
        $this->seatsioClient->subaccounts->create();

        $subaccounts = $this->seatsioClient->subaccounts->listAll((new SubaccountListParams())->withFilter('subaccount2'));
        $subaccountIds = \Functional\map($subaccounts, function($subaccount) { return $subaccount->id; });

        self::assertEquals([$subaccount2->id], array_values($subaccountIds));
    }

    public function testWithFilterContainingSpecialCharacter()
    {
        $createdSubaccountIds = array();

        for ($i = 0; $i < 55; $i++) {
            $subaccount = $this->seatsioClient->subaccounts->create("test-/@/" . $i);
            if($i == 4 || ($i >= 40 && $i <=49)) {
                $createdSubaccountIds[] = $subaccount->id;
            }
        }

        $subaccounts = $this->seatsioClient->subaccounts->listAll((new SubaccountListParams())->withFilter('test-/@/4'));
        $retrievedSubaccountIds = \Functional\map($subaccounts, function($subaccount) { return $subaccount->id; });

        self::assertEquals($createdSubaccountIds, $retrievedSubaccountIds, "", 0.0, 10, true);
    }

    public function testWithFilterNoResult()
    {
        $subaccounts = $this->seatsioClient->subaccounts->listAll((new SubaccountListParams())->withFilter('test'));
        $subaccountIds = \Functional\map($subaccounts, function($subaccount) { return $subaccount->id; });

        self::assertEmpty(array_values($subaccountIds));
    }

    public function testWithFilterFirstPage()
    {
        $this->seatsioClient->subaccounts->create("test-/@/1");
        $subaccount2 = $this->seatsioClient->subaccounts->create("test-/@/2");
        $this->seatsioClient->subaccounts->create("test-/@/3");

        $subaccounts = $this->seatsioClient->subaccounts->listFirstPage(null, (new SubaccountListParams())->withFilter('test-/@/2'));
        $subaccountIds = \Functional\map($subaccounts->items, function($subaccount) { return $subaccount->id; });

        self::assertEquals([$subaccount2->id], array_values($subaccountIds));
        self::assertEmpty($subaccounts->nextPageStartsAfter);
        self::assertEmpty($subaccounts->previousPageEndsBefore);
    }

    public function testWithFilterPreviousPage()
    {
        $subaccount1 = $this->seatsioClient->subaccounts->create("test-/@/11");
        $subaccount2 = $this->seatsioClient->subaccounts->create("test-/@/12");
        $subaccount3 = $this->seatsioClient->subaccounts->create("test-/@/33");
        $this->seatsioClient->subaccounts->create("test-/@/14");
        $this->seatsioClient->subaccounts->create("test-/@/5");
        $this->seatsioClient->subaccounts->create("test-/@/6");
        $this->seatsioClient->subaccounts->create("test-/@/7");
        $this->seatsioClient->subaccounts->create("test-/@/8");

        $subaccounts = $this->seatsioClient->subaccounts->listPageAfter($subaccount3->id, null, (new SubaccountListParams())->withFilter('test-/@/1'));
        $subaccountIds = \Functional\map($subaccounts->items, function($subaccount) { return $subaccount->id; });

        self::assertEquals([$subaccount1->id, $subaccount2->id], $subaccountIds, "", 0.0, 10, true);
        self::assertEmpty($subaccounts->nextPageStartsAfter);
        self::assertEquals($subaccount2->id, $subaccounts->previousPageEndsBefore);
    }

    public function testWithFilterNextPage()
    {
        $subaccount1 = $this->seatsioClient->subaccounts->create("test-/@/11");
        $subaccount2 = $this->seatsioClient->subaccounts->create("test-/@/12");
        $subaccount3 = $this->seatsioClient->subaccounts->create("test-/@/13");
        $this->seatsioClient->subaccounts->create("test-/@/4");
        $this->seatsioClient->subaccounts->create("test-/@/5");
        $this->seatsioClient->subaccounts->create("test-/@/6");
        $this->seatsioClient->subaccounts->create("test-/@/7");
        $this->seatsioClient->subaccounts->create("test-/@/8");

        $subaccounts = $this->seatsioClient->subaccounts->listPageBefore($subaccount1->id, null, (new SubaccountListParams())->withFilter('test-/@/1'));
        $subaccountIds = \Functional\map($subaccounts->items, function($subaccount) { return $subaccount->id; });

        self::assertEquals([$subaccount3->id, $subaccount2->id], $subaccountIds, "", 0.0, 10, true);
        self::assertEmpty($subaccounts->previousPageEndsBefore);
        self::assertEquals($subaccount2->id, $subaccounts->nextPageStartsAfter);
    }
}
