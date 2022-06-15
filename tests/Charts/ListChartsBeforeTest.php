<?php

namespace Seatsio\Charts;

use Seatsio\SeatsioClientTest;
use function Functional\map;

class ListChartsBeforeTest extends SeatsioClientTest
{

    public function test()
    {
        $chart1 = $this->seatsioClient->charts->create();
        $chart2 = $this->seatsioClient->charts->create();
        $chart3 = $this->seatsioClient->charts->create();

        $page = $this->seatsioClient->charts->listPageBefore($chart1->id);
        $chartKeys = map($page->items, function($chart) { return $chart->key; });

        self::assertEquals([$chart3->key, $chart2->key], array_values($chartKeys));
    }

    public function testFilter()
    {
        $chart1 = $this->seatsioClient->charts->create('foo');
        $chart2 = $this->seatsioClient->charts->create('foo');
        $chart3 = $this->seatsioClient->charts->create('bar');
        $chart4 = $this->seatsioClient->charts->create('foo');

        $page = $this->seatsioClient->charts->listPageBefore($chart1->id, (new ChartListParams())->withFilter('foo'));
        $chartKeys = map($page->items, function($chart) { return $chart->key; });

        self::assertEquals([$chart4->key, $chart2->key], array_values($chartKeys));
    }

    public function testPagesize()
    {
        $chart1 = $this->seatsioClient->charts->create();
        $chart2 = $this->seatsioClient->charts->create();
        $chart3 = $this->seatsioClient->charts->create();
        $chart4 = $this->seatsioClient->charts->create();

        $page = $this->seatsioClient->charts->listPageBefore($chart1->id, null, 2);
        $chartKeys = map($page->items, function($chart) { return $chart->key; });

        self::assertEquals([$chart3->key, $chart2->key], array_values($chartKeys));
    }

}
