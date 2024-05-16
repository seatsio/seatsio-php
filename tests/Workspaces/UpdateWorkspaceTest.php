<?php

namespace Seatsio\Workspaces;

use Seatsio\SeatsioClientTest;

class UpdateWorkspaceTest extends SeatsioClientTest
{

    public function test()
    {
        $workspace = $this->seatsioClient->workspaces->create("my workspace");

        $this->seatsioClient->workspaces->update($workspace->key, "my ws");

        $retrievedWorkspace = $this->seatsioClient->workspaces->retrieve($workspace->key);
        self::assertEquals("my ws", $retrievedWorkspace->name);
        self::assertNotNull($retrievedWorkspace->key);
        self::assertNotNull($retrievedWorkspace->secretKey);
        self::assertFalse($retrievedWorkspace->isTest);
    }

}
