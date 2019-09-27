<?php

namespace Seatsio;

class AccountIDAuthenticationTest extends \Seatsio\SeatsioClientTest
{

    public function testClientTakesOptionalAccountId()
    {
        $subaccount = $this->seatsioClient->subaccounts->create();

        $subaccountClient = self::createSeatsioClient($this->user->secretKey, $subaccount->accountId);
        $holdToken = $subaccountClient->holdTokens->create();

        self::assertEquals($subaccount->accountId, $holdToken->accountId);
    }

}
