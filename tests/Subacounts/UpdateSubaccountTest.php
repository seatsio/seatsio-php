<?php

namespace Seatsio\Subaccounts;

use Seatsio\SeatsioClientTest;

class UpdateSubaccountTest extends SeatsioClientTest
{

    public function test()
    {
        $subaccount = $this->seatsioClient->subaccounts->create('joske');
        $email = $this->randomEmail();

        $this->seatsioClient->subaccounts->update($subaccount->id, 'jefke', $email);

        $retrievedSubaccount = $this->seatsioClient->subaccounts->retrieve($subaccount->id);
        self::assertEquals('jefke', $retrievedSubaccount->name);
        self::assertEquals($email, $retrievedSubaccount->email);
    }

    public function testEmailIsOptional()
    {
        $email = $this->randomEmail();
        $subaccount = $this->seatsioClient->subaccounts->createWithEmail($email, 'joske');

        $this->seatsioClient->subaccounts->update($subaccount->id, 'jefke');

        $retrievedSubaccount = $this->seatsioClient->subaccounts->retrieve($subaccount->id);
        self::assertEquals('jefke', $retrievedSubaccount->name);
        self::assertEquals($email, $retrievedSubaccount->email);
    }

    public function testNameIsOptional()
    {
        $email = $this->randomEmail();
        $subaccount = $this->seatsioClient->subaccounts->create('joske');

        $this->seatsioClient->subaccounts->update($subaccount->id, null, $email);

        $retrievedSubaccount = $this->seatsioClient->subaccounts->retrieve($subaccount->id);
        self::assertEquals('joske', $retrievedSubaccount->name);
        self::assertEquals($email, $retrievedSubaccount->email);
    }
}