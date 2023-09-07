<?php

namespace Seatsio;

class WorkspaceKeyAuthenticationTest extends \Seatsio\SeatsioClientTest
{

    public function testClientTakesOptionalWorkspaceKey()
    {
        $workspace = $this->seatsioClient->workspaces->create('some workspace');

        $workspaceClient = self::createSeatsioClient($this->user->secretKey, $workspace->key);
        $holdToken = $workspaceClient->holdTokens->create();

        self::assertEquals($workspace->key, $holdToken->workspaceKey);
    }

}
