<?php

namespace Seatsio;

class WorkspaceKeyAuthenticationTest extends \Seatsio\SeatsioClientTest
{

    public function testClientTakesOptionalWorkspaceKey()
    {
        $subaccount = $this->seatsioClient->subaccounts->create();

        $subaccountClient = self::createSeatsioClient($this->user->secretKey, $subaccount->workspace->key);
        $holdToken = $subaccountClient->holdTokens->create();

        self::assertEquals($subaccount->workspace->key, $holdToken->workspaceKey);
    }

}
