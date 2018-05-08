<?php

namespace Seatsio\Charts;

use Seatsio\SeatsioClientTest;

class MoveChartOutOfArchiveTest extends SeatsioClientTest
{

    public function test()
    {
        $chart = $this->seatsioClient->charts()->create();
        $this->seatsioClient->charts()->moveToArchive($chart->key);

        $this->seatsioClient->charts()->moveOutOfArchive($chart->key);

        $archivedCharts = $this->seatsioClient->charts()->archive->all();
        self::assertFalse($archivedCharts->valid());
    }

}