<?php

namespace Seatsio\Workspaces;

use DateTime;
use Seatsio\SeatsioClientTest;

class RetrieveWorkspaceTest extends SeatsioClientTest
{

    public function test()
    {
        $workspace = $this->seatsioClient->workspaces->create("my workspace");

        $retrievedWorkspace = $this->seatsioClient->workspaces->retrieve($workspace->key);

        self::assertEquals("my workspace", $retrievedWorkspace->name);
        self::assertNotNull($retrievedWorkspace->key);
        self::assertNotNull($retrievedWorkspace->secretKey);
        self::assertFalse($retrievedWorkspace->isTest);
        self::assertTrue($retrievedWorkspace->isActive);
    }

}
