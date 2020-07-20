<?php

namespace Seatsio\Subaccounts;

use Seatsio\SeatsioClientTest;

class SetDefaultWorkspaceTest extends SeatsioClientTest
{

    public function test()
    {
        $workspace = $this->seatsioClient->workspaces->create("a ws");

        $this->seatsioClient->workspaces->setDefault($workspace->key);

        $retrievedWorkspace = $this->seatsioClient->workspaces->retrieve($workspace->key);
        self::assertTrue($retrievedWorkspace->isDefault);
    }
}
