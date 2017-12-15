<?php

namespace Seatsio;

class ListChartsTests extends SeatsioClientTest
{

    public function testListChartsInOnePage()
    {
        $this->seatsioClient->setPageSize(10);
        $chartKey1 = $this->seatsioClient->createChart();
        $chartKey2 = $this->seatsioClient->createChart();
        $chartKey3 = $this->seatsioClient->createChart();

        $charts = $this->seatsioClient->listCharts();
        $chartKeys = \Functional\map($charts, function($chart) { return $chart->key; });

        self::assertEquals([$chartKey3, $chartKey2, $chartKey1], array_values($chartKeys));
    }

    public function testListEmptyCollectionOfCharts()
    {
        $charts = $this->seatsioClient->listCharts();

        self::assertFalse($charts->valid());
    }

    public function testListChartsInMultiplePagesReturnsProperIterator()
    {
        $this->seatsioClient->setPageSize(2);
        $chartKey1 = $this->seatsioClient->createChart();
        $chartKey2 = $this->seatsioClient->createChart();
        $chartKey3 = $this->seatsioClient->createChart();

        $charts = $this->seatsioClient->listCharts();
        $chartKeys = \Functional\map($charts, function($chart) { return $chart->key; });

        self::assertEquals([$chartKey3, $chartKey2, $chartKey1], array_values($chartKeys));
    }

}