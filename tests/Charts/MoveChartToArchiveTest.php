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
        $archivedChartKeys = array_map(function($chart) { return $chart->key; }, iterator_to_array($archivedCharts));

        self::assertEquals([$chart->key], array_values($archivedChartKeys));
    }

}
