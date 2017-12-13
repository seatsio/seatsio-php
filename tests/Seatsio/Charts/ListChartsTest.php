<?php

namespace Seatsio;

class ListChartsTests extends SeatsioClientTest
{

    public function testListCharts()
    {
        $chartKey1 = $this->seatsioClient->createChart();
        $chartKey2 = $this->seatsioClient->createChart();
        $chartKey3 = $this->seatsioClient->createChart();

        $chartsIterator = $this->seatsioClient->listCharts();
        $charts = $chartsIterator->current();

        $chartsIterator->next();
        self::assertFalse($chartsIterator->valid());
        self::assertCount(3, $charts);
        self::assertEquals($chartKey3, $charts[0]->key);
        self::assertEquals($chartKey2, $charts[1]->key);
        self::assertEquals($chartKey1, $charts[2]->key);
    }

    public function testListChartsInMultiplePages()
    {
        $chartKey1 = $this->seatsioClient->createChart();
        $chartKey2 = $this->seatsioClient->createChart();
        $chartKey3 = $this->seatsioClient->createChart();

        $chartsIterator = $this->seatsioClient->listCharts(2);
        $charts = $chartsIterator->current();

        self::assertCount(2, $charts);
        self::assertEquals($chartKey3, $charts[0]->key);
        self::assertEquals($chartKey2, $charts[1]->key);

        $chartsIterator->next();
        $charts = $chartsIterator->current();

        self::assertCount(1, $charts);
        self::assertEquals($chartKey1, $charts[0]->key);

        $chartsIterator->next();
        self::assertFalse($chartsIterator->valid());
    }

    public function testListChartsInMultiplePagesReturnsProperIterator()
    {
        $chartKey1 = $this->seatsioClient->createChart();
        $chartKey2 = $this->seatsioClient->createChart();
        $chartKey3 = $this->seatsioClient->createChart();

        $chartsPages = $this->seatsioClient->listCharts(2);
        $chartKeys = \Functional\map(\Functional\flatten($chartsPages), function($chart) { return $chart->key; });

        self::assertEquals([$chartKey3, $chartKey2, $chartKey1], $chartKeys);
    }

}