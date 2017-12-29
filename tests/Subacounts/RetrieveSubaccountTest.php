<?php

namespace Seatsio\Subaccounts;

use Seatsio\SeatsioClientTest;

class RetrieveSubaccountTest extends SeatsioClientTest
{

    public function test()
    {
        $subaccount = $this->seatsioClient->subaccounts()->create('joske');

        $retrievedSubaccount = $this->seatsioClient->subaccounts()->retrieve($subaccount->id);

        self::assertEquals($subaccount->id, $retrievedSubaccount->id);
        self::assertNotEmpty($retrievedSubaccount->secretKey);
        self::assertNotEmpty($retrievedSubaccount->designerKey);
        self::assertNotEmpty($retrievedSubaccount->publicKey);
        self::assertEquals('joske', $retrievedSubaccount->name);
        self::assertTrue($retrievedSubaccount->active);
    }
}