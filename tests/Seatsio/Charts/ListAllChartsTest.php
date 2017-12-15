<?php

namespace Seatsio;

class ListAllChartsTest extends SeatsioClientTest
{

    public function testOnePage()
    {
        $this->seatsioClient->setPageSize(10);
        $chart1 = $this->seatsioClient->charts()->create();
        $chart2 = $this->seatsioClient->charts()->create();
        $chart3 = $this->seatsioClient->charts()->create();

        $charts = $this->seatsioClient->charts()->lister()->all();
        $chartKeys = \Functional\map($charts, function($chart) { return $chart->key; });

        self::assertEquals([$chart3->key, $chart2->key, $chart1->key], array_values($chartKeys));
    }

    public function testNoCharts()
    {
        $charts = $this->seatsioClient->charts()->lister()->all();

        self::assertFalse($charts->valid());
    }

    public function testMultiplePages()
    {
        $this->seatsioClient->setPageSize(2);
        $chart1 = $this->seatsioClient->charts()->create();
        $chart2 = $this->seatsioClient->charts()->create();
        $chart3 = $this->seatsioClient->charts()->create();

        $charts = $this->seatsioClient->charts()->lister()->all();
        $chartKeys = \Functional\map($charts, function($chart) { return $chart->key; });

        self::assertEquals([$chart3->key, $chart2->key, $chart1->key], array_values($chartKeys));
    }

}