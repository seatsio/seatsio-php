<?php

namespace Seatsio\Subaccounts;

use Seatsio\SeatsioClientTest;

class RegenerateWorkspaceSecretKeyTest extends SeatsioClientTest
{

    public function test()
    {
        $workspace = $this->seatsioClient->workspaces->create("a ws");

        $newSecretKey = $this->seatsioClient->workspaces->regenerateSecretKey($workspace->key);

        self::assertNotNull($newSecretKey);
        self::assertNotEquals($workspace->secretKey, $newSecretKey);
        $retrievedWorkspace = $this->seatsioClient->workspaces->retrieve($workspace->key);
        self::assertEquals($retrievedWorkspace->secretKey, $newSecretKey);
    }
}
