<?php

namespace Seatsio\Subaccounts;

use Seatsio\SeatsioClientTest;

class UpdateSubaccountTest extends SeatsioClientTest
{

    public function test()
    {
        $subaccount = $this->seatsioClient->subaccounts()->create('joske');

        $this->seatsioClient->subaccounts()->update($subaccount->id, 'jefke');

        $retrievedSubaccount = $this->seatsioClient->subaccounts()->retrieve($subaccount->id);
        self::assertEquals('jefke', $retrievedSubaccount->name);
    }
}