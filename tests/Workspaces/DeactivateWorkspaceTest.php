<?php

namespace Seatsio\Workspaces;

use Seatsio\SeatsioClientTest;

class DeactivateWorkspaceTest extends SeatsioClientTest
{

    public function test()
    {
        $workspace = $this->seatsioClient->workspaces->create("a ws");

        $this->seatsioClient->workspaces->deactivate($workspace->key);

        $retrievedWorkspace = $this->seatsioClient->workspaces->retrieve($workspace->key);
        self::assertFalse($retrievedWorkspace->isActive);
    }
}
