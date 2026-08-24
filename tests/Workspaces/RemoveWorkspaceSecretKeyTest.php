<?php

namespace Workspaces;

use Seatsio\SeatsioClientTest;

class RemoveWorkspaceSecretKeyTest extends SeatsioClientTest
{

    public function test()
    {
        $workspace = $this->seatsioClient->workspaces->create("a ws");
        $newSecretKey = $this->seatsioClient->workspaces->addSecretKey($workspace->key);

        $retrievedWorkspace = $this->seatsioClient->workspaces->retrieve($workspace->key);
        self::assertContains($workspace->secretKey, $retrievedWorkspace->secretKeys);
        self::assertContains($newSecretKey, $retrievedWorkspace->secretKeys);

        $this->seatsioClient->workspaces->removeSecretKey($workspace->key, $workspace->secretKey);

        $finalStateWorkspace = $this->seatsioClient->workspaces->retrieve($workspace->key);
        self::assertContains($newSecretKey, $finalStateWorkspace->secretKeys);
        self::assertNotContains($workspace->secretKey, $finalStateWorkspace->secretKeys);
    }
}
