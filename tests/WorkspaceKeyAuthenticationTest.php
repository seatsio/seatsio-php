<?php

namespace Seatsio;

class WorkspaceKeyAuthenticationTest extends \Seatsio\SeatsioClientTest
{

    public function testClientTakesOptionalWorkspaceKey()
    {
        $subaccount = $this->seatsioClient->subaccounts->create();

        $subaccountClient = self::createSeatsioClient($this->user->secretKey, $subaccount->publicKey);
        $holdToken = $subaccountClient->holdTokens->create();

        self::assertEquals($subaccount->publicKey, $holdToken->workspaceKey);
    }

}
