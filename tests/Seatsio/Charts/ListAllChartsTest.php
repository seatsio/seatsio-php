<?php

namespace Seatsio;

class ListAllChartsTest extends SeatsioClientTest
{

    public function testOnePage()
    {
        $this->seatsioClient->setPageSize(10);
        $chart1 = $this->seatsioClient->createChart();
        $chart2 = $this->seatsioClient->createChart();
        $chart3 = $this->seatsioClient->createChart();

        $charts = $this->seatsioClient->listAllCharts();
        $chartKeys = \Functional\map($charts, function($chart) { return $chart->key; });

        self::assertEquals([$chart3->key, $chart2->key, $chart1->key], array_values($chartKeys));
    }

    public function testNoCharts()
    {
        $charts = $this->seatsioClient->listAllCharts();

        self::assertFalse($charts->valid());
    }

    public function testMultiplePages()
    {
        $this->seatsioClient->setPageSize(2);
        $chart1 = $this->seatsioClient->createChart();
        $chart2 = $this->seatsioClient->createChart();
        $chart3 = $this->seatsioClient->createChart();

        $charts = $this->seatsioClient->listAllCharts();
        $chartKeys = \Functional\map($charts, function($chart) { return $chart->key; });

        self::assertEquals([$chart3->key, $chart2->key, $chart1->key], array_values($chartKeys));
    }

}