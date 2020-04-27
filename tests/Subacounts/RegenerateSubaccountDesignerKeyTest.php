<?php

namespace Seatsio\Subaccounts;

use Seatsio\SeatsioClientTest;

class RegenerateSubaccountDesignerKeyTest extends SeatsioClientTest
{

    public function test()
    {
        $subaccount = $this->seatsioClient->subaccounts->create();

        $newDesignerKey = $this->seatsioClient->subaccounts->regenerateDesignerKey($subaccount->id);

        self::assertNotNull($newDesignerKey);
        self::assertNotEquals($subaccount->designerKey, $newDesignerKey);
    }
}
