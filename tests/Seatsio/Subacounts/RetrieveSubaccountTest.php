<?php

namespace Seatsio\Subaccounts;

use Seatsio\SeatsioClientTest;

class RetrieveSubaccountTest extends SeatsioClientTest
{

    public function test()
    {
        $subaccount = $this->seatsioClient->subaccounts()->create('joske');

        $retrievedSubaccount = $this->seatsioClient->subaccounts()->retrieve($subaccount->id);

        self::assertNotEmpty($retrievedSubaccount->secretKey);
        self::assertNotEmpty($retrievedSubaccount->designerKey);
        self::assertNotEmpty($retrievedSubaccount->publicKey);
        self::assertEquals($retrievedSubaccount->name, 'joske');
        self::assertTrue($retrievedSubaccount->active);
    }
}