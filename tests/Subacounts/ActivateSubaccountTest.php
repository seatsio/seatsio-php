<?php

namespace Seatsio\Subaccounts;

use Seatsio\SeatsioClientTest;

class ActivateSubaccountTest extends SeatsioClientTest
{

    public function test()
    {
        $subaccount = $this->seatsioClient->subaccounts()->create('joske');
        $this->seatsioClient->subaccounts()->deactivate($subaccount->id);

        $this->seatsioClient->subaccounts()->activate($subaccount->id);

        $retrievedSubaccount = $this->seatsioClient->subaccounts()->retrieve($subaccount->id);
        self::assertTrue($retrievedSubaccount->active);
    }
}