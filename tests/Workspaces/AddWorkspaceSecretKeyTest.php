<?php

namespace Workspaces;

use Seatsio\SeatsioClientTest;

class AddWorkspaceSecretKeyTest extends SeatsioClientTest
{

    public function test()
    {
        $workspace = $this->seatsioClient->workspaces->create("a ws");

        $newSecretKey = $this->seatsioClient->workspaces->addSecretKey($workspace->key);

        self::assertNotNull($newSecretKey);
        self::assertNotEquals($workspace->secretKey, $newSecretKey);
        $retrievedWorkspace = $this->seatsioClient->workspaces->retrieve($workspace->key);
        self::assertContains($workspace->secretKey, $retrievedWorkspace->secretKeys);
        self::assertContains($newSecretKey, $retrievedWorkspace->secretKeys);
    }
}
