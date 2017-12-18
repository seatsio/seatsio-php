<?php

namespace Seatsio\Subaccounts;

use Seatsio\SeatsioClientTest;

class DeactivateSubaccountTest extends SeatsioClientTest
{

    public function test()
    {
        $subaccount = $this->seatsioClient->subaccounts()->create();

        $this->seatsioClient->subaccounts()->deactivate($subaccount->id);

        $retrievedSubaccount = $this->seatsioClient->subaccounts()->retrieve($subaccount->id);
        self::assertFalse($retrievedSubaccount->active);
    }
}