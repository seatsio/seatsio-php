<?php

namespace Seatsio;

class ListChartsAfterTest extends SeatsioClientTest
{

    public function test()
    {
        $chart1 = $this->seatsioClient->charts()->create();
        $chart2 = $this->seatsioClient->charts()->create();
        $chart3 = $this->seatsioClient->charts()->create();

        $page = $this->seatsioClient->charts()->lister()->pageAfter($chart3->id);
        $chartKeys = \Functional\map($page->items, function($chart) { return $chart->key; });

        self::assertEquals([$chart2->key, $chart1->key], array_values($chartKeys));
    }

}