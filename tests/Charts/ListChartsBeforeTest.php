<?php

namespace Seatsio\Charts;

use Seatsio\SeatsioClientTest;

class ListChartsBeforeTest extends SeatsioClientTest
{

    public function test()
    {
        $chart1 = $this->seatsioClient->charts()->create();
        $chart2 = $this->seatsioClient->charts()->create();
        $chart3 = $this->seatsioClient->charts()->create();

        $page = $this->seatsioClient->charts()->iterator()->pageBefore($chart1->id);
        $chartKeys = \Functional\map($page->items, function($chart) { return $chart->key; });

        self::assertEquals([$chart3->key, $chart2->key], array_values($chartKeys));
    }

}