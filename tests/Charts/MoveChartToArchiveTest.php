<?php

namespace Seatsio\Charts;

use Seatsio\SeatsioClientTest;
use function Functional\map;

class MoveChartToArchiveTest extends SeatsioClientTest
{

    public function test()
    {
        $chart = $this->seatsioClient->charts->create();

        $this->seatsioClient->charts->moveToArchive($chart->key);

        $archivedCharts = $this->seatsioClient->charts->archive->all();
        $archivedChartKeys = map($archivedCharts, function($chart) { return $chart->key; });

        self::assertEquals([$chart->key], array_values($archivedChartKeys));
    }

}
