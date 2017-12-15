<?php

namespace Seatsio;

class ListChartsBeforeTest extends SeatsioClientTest
{

    public function test()
    {
        $chart1 = $this->seatsioClient->createChart();
        $chart2 = $this->seatsioClient->createChart();
        $chart3 = $this->seatsioClient->createChart();

        $page = $this->seatsioClient->listChartsBefore($chart1->id);
        $chartKeys = \Functional\map($page->items, function($chart) { return $chart->key; });

        self::assertEquals([$chart3->key, $chart2->key], array_values($chartKeys));
    }

}