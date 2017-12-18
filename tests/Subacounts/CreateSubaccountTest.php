<?php

namespace Seatsio\Subaccounts;

use Seatsio\SeatsioClientTest;

class CreateSubaccountTest extends SeatsioClientTest
{

    public function test()
    {
        $subaccount = $this->seatsioClient->subaccounts()->create('joske');

        self::assertNotEmpty($subaccount->secretKey);
        self::assertNotEmpty($subaccount->designerKey);
        self::assertNotEmpty($subaccount->publicKey);
        self::assertEquals($subaccount->name, 'joske');
        self::assertTrue($subaccount->active);
    }
}