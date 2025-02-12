<?php

namespace Seatsio\Charts;

use Seatsio\SeatsioClientTest;

class ListChartsInArchiveTest extends SeatsioClientTest
{

    public function test()
    {
        $chart1 = $this->seatsioClient->charts->create();
        $this->seatsioClient->charts->moveToArchive($chart1->key);

        $chart2 = $this->seatsioClient->charts->create();
        $this->seatsioClient->charts->moveToArchive($chart2->key);

        $this->seatsioClient->charts->create();

        $charts = $this->seatsioClient->charts->archive->all();
        $chartKeys = array_map(function($chart) { return $chart->key; }, iterator_to_array($charts));

        self::assertEquals([$chart2->key, $chart1->key], array_values($chartKeys));
    }

}
