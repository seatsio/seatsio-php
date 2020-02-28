<?php

namespace Seatsio\Charts;

use Seatsio\SeatsioClientTest;

class CopyChartToWorkspaceTest extends SeatsioClientTest
{

    public function test()
    {
        $chart = $this->seatsioClient->charts->create('my chart');
        $workspace = $this->seatsioClient->workspaces->create('my ws');
        $workspaceSeatsioClient = self::createSeatsioClient($workspace->secretKey);

        $copiedChart = $this->seatsioClient->charts->copyToWorkspace($chart->key, $workspace->key);

        self::assertEquals('my chart', $copiedChart->name);
        self::assertNotEquals($chart->key, $copiedChart->key);
        $retrievedChart = $workspaceSeatsioClient->charts->retrieve($copiedChart->key);
        self::assertEquals('my chart', $retrievedChart->name);
    }

}
