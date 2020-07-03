<?php

namespace Seatsio\Workspaces;

use DateTime;
use Seatsio\SeatsioClientTest;

class CreateWorkspaceTest extends SeatsioClientTest
{

    public function testCreateWorkspace()
    {
        $workspace = $this->seatsioClient->workspaces->create("my workspace");

        self::assertEquals("my workspace", $workspace->name);
        self::assertNotNull($workspace->key);
        self::assertNotNull($workspace->secretKey);
        self::assertFalse($workspace->isTest);
        self::assertTrue($workspace->isActive);
    }

    public function testCreateTestWorkspace()
    {
        $workspace = $this->seatsioClient->workspaces->create("my workspace", true);

        self::assertEquals("my workspace", $workspace->name);
        self::assertNotNull($workspace->key);
        self::assertNotNull($workspace->secretKey);
        self::assertTrue($workspace->isTest);
        self::assertTrue($workspace->isActive);
    }

}
