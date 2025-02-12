<?php

namespace Seatsio\Charts;

use Seatsio\SeatsioClientTest;

class ListWorkspacesTest extends SeatsioClientTest
{

    public function test()
    {
        $this->seatsioClient->workspaces->create("ws1");
        $ws2 = $this->seatsioClient->workspaces->create("ws2");
        $this->seatsioClient->workspaces->deactivate($ws2->key);
        $this->seatsioClient->workspaces->create("ws3");

        $workspaces = $this->seatsioClient->workspaces->listAll();
        $workspaceNames = array_map(function ($workspace) {
            return $workspace->name;
        }, iterator_to_array($workspaces));

        self::assertEquals(["ws3", "ws2", "ws1", "Production workspace"], array_values($workspaceNames));
    }

    public function test_filter()
    {
        $this->seatsioClient->workspaces->create("someWorkspace");
        $ws = $this->seatsioClient->workspaces->create("anotherWorkspace");
        $this->seatsioClient->workspaces->deactivate($ws->key);
        $this->seatsioClient->workspaces->create("anotherAnotherWorkspace");

        $workspaces = $this->seatsioClient->workspaces->listAll("another");
        $workspaceNames = array_map(function ($workspace) {
            return $workspace->name;
        }, iterator_to_array($workspaces));

        self::assertEquals(["anotherAnotherWorkspace", "anotherWorkspace"], array_values($workspaceNames));
    }

}
