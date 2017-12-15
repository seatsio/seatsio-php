<?php

namespace Seatsio;

class ListChartsTest extends SeatsioClientTest
{

    public function testNoAfterId()
    {
        $chart1 = $this->seatsioClient->createChart();
        $chart2 = $this->seatsioClient->createChart();
        $chart3 = $this->seatsioClient->createChart();

        $page = $this->seatsioClient->listCharts();
        $chartKeys = \Functional\map($page->items, function($chart) { return $chart->key; });

        self::assertEquals([$chart3->key, $chart2->key, $chart1->key], array_values($chartKeys));
    }

    public function testAfterId()
    {
        $chart1 = $this->seatsioClient->createChart();
        $chart2 = $this->seatsioClient->createChart();
        $chart3 = $this->seatsioClient->createChart();

        $page = $this->seatsioClient->listCharts($chart3->id);
        $chartKeys = \Functional\map($page->items, function($chart) { return $chart->key; });

        self::assertEquals([$chart2->key, $chart1->key], array_values($chartKeys));
    }

}