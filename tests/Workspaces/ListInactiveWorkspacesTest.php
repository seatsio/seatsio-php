<?php

namespace Seatsio\Charts;

use Seatsio\SeatsioClientTest;
use function Functional\map;

class ListInactiveWorkspacesTest extends SeatsioClientTest
{

    public function test()
    {
        $ws1 = $this->seatsioClient->workspaces->create("ws1");
        $this->seatsioClient->workspaces->deactivate($ws1->key);
        $this->seatsioClient->workspaces->create("ws2");
        $ws3 = $this->seatsioClient->workspaces->create("ws3");
        $this->seatsioClient->workspaces->deactivate($ws3->key);

        $workspaces = $this->seatsioClient->workspaces->inactive->all();
        $workspaceNames = map($workspaces, function ($workspace) {
            return $workspace->name;
        });

        self::assertEquals(["ws3", "ws1"], array_values($workspaceNames));
    }

    public function test_filter()
    {
        $this->seatsioClient->workspaces->create("someWorkspace");
        $ws1 = $this->seatsioClient->workspaces->create("anotherWorkspace");
        $this->seatsioClient->workspaces->deactivate($ws1->key);
        $ws2 = $this->seatsioClient->workspaces->create("anotherAnotherWorkspace");
        $this->seatsioClient->workspaces->deactivate($ws2->key);
        $this->seatsioClient->workspaces->create("anotherAnotherAnotherWorkspace");

        $workspaces = $this->seatsioClient->workspaces->inactive->all("another");
        $workspaceNames = map($workspaces, function ($workspace) {
            return $workspace->name;
        });

        self::assertEquals(["anotherAnotherWorkspace", "anotherWorkspace"], array_values($workspaceNames));
    }

}
