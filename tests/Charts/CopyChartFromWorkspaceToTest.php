<?php

namespace Charts;

use Seatsio\SeatsioClientTest;

class CopyChartFromWorkspaceToTest extends SeatsioClientTest
{

    public function test()
    {
        $chart = $this->seatsioClient->charts->create('my chart');
        $toWorkspace = $this->seatsioClient->workspaces->create('my ws');
        $toWorkspaceSeatsioClient = self::createSeatsioClient($toWorkspace->secretKey);

        $copiedChart = $this->seatsioClient->charts->copyFromWorkspaceTo($chart->key, $this->workspace->key, $toWorkspace->key);

        self::assertEquals('my chart', $copiedChart->name);
        self::assertNotEquals($chart->key, $copiedChart->key);
        $retrievedChart = $toWorkspaceSeatsioClient->charts->retrieve($copiedChart->key);
        self::assertEquals('my chart', $retrievedChart->name);
    }

}
