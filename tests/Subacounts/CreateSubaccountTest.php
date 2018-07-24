<?php

namespace Seatsio\Subaccounts;

use Seatsio\SeatsioClientTest;

class CreateSubaccountTest extends SeatsioClientTest
{

    public function test()
    {
        $subaccount = $this->seatsioClient->subaccounts->create('joske');

        self::assertNotEmpty($subaccount->secretKey);
        self::assertNotEmpty($subaccount->designerKey);
        self::assertNotEmpty($subaccount->publicKey);
        self::assertEquals('joske', $subaccount->name);
        self::assertTrue($subaccount->active);
    }

    public function testNameIsOptional()
    {
        $subaccount = $this->seatsioClient->subaccounts->create();

        self::assertNull($subaccount->name);
    }

    public function testWithEmail()
    {
        $randomEmail = $this->randomEmail();
        $subaccount = $this->seatsioClient->subaccounts->createWithEmail($randomEmail);

        self::assertNotEmpty($subaccount->secretKey);
        self::assertNotEmpty($subaccount->designerKey);
        self::assertNotEmpty($subaccount->publicKey);
        self::assertEmpty($subaccount->name);
        self::assertTrue($subaccount->active);
        self::assertEquals($randomEmail, $subaccount->email);
    }
}