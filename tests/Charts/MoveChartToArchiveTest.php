<?php

namespace Seatsio\Charts;

use Seatsio\SeatsioClientTest;

class MoveChartToArchiveTest extends SeatsioClientTest
{

    public function test()
    {
        $chart = $this->seatsioClient->charts->create();

        $this->seatsioClient->charts->moveToArchive($chart->key);

        $archivedCharts = $this->seatsioClient->charts->archive->all();
        $archivedChartKeys = \Functional\map($archivedCharts, function($chart) { return $chart->key; });

        self::assertEquals([$chart->key], array_values($archivedChartKeys));
    }

}