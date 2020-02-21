<?php

namespace Seatsio\Charts;

use Seatsio\SeatsioClientTest;

class ListWorkspacesTest extends SeatsioClientTest
{

    public function test()
    {
        $this->seatsioClient->workspaces->create("ws1");
        $this->seatsioClient->workspaces->create("ws2");
        $this->seatsioClient->workspaces->create("ws3");

        $workspaces = $this->seatsioClient->workspaces->listAll();
        $workspaceNames = \Functional\map($workspaces, function($workspace) { return $workspace->name; });

        self::assertEquals(["ws3", "ws2", "ws1", "Main workspace"], array_values($workspaceNames));
    }

}
