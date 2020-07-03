<?php

namespace Seatsio\Subaccounts;

use Seatsio\SeatsioClientTest;

class ActivateWorkspaceTest extends SeatsioClientTest
{

    public function test()
    {
        $workspace = $this->seatsioClient->workspaces->create("a ws");
        $this->seatsioClient->workspaces->deactivate($workspace->key);

        $this->seatsioClient->workspaces->activate($workspace->key);

        $retrievedWorkspace = $this->seatsioClient->workspaces->retrieve($workspace->key);
        self::assertTrue($retrievedWorkspace->isActive);
    }
}
