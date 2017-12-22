<?php

namespace Seatsio\Charts;

use Seatsio\SeatsioClientTest;

class ListAllChartsTest extends SeatsioClientTest
{

    public function testOnePage()
    {
        $chart1 = $this->seatsioClient->charts()->create();
        $chart2 = $this->seatsioClient->charts()->create();
        $chart3 = $this->seatsioClient->charts()->create();

        $charts = $this->seatsioClient->charts()->lister()->setPageSize(10)->all();
        $chartKeys = \Functional\map($charts, function ($chart) {
            return $chart->key;
        });

        self::assertEquals([$chart3->key, $chart2->key, $chart1->key], array_values($chartKeys));
    }

    public function testNoCharts()
    {
        $charts = $this->seatsioClient->charts()->lister()->all();

        self::assertFalse($charts->valid());
    }

    public function testMultiplePages()
    {
        $chart1 = $this->seatsioClient->charts()->create();
        $chart2 = $this->seatsioClient->charts()->create();
        $chart3 = $this->seatsioClient->charts()->create();

        $charts = $this->seatsioClient->charts()->lister()->setPageSize(2)->all();
        $chartKeys = \Functional\map($charts, function ($chart) {
            return $chart->key;
        });

        self::assertEquals([$chart3->key, $chart2->key, $chart1->key], array_values($chartKeys));
    }

    public function testFilter()
    {
        $chart1 = $this->seatsioClient->charts()->create('foo');
        $chart2 = $this->seatsioClient->charts()->create('bar');
        $chart3 = $this->seatsioClient->charts()->create('foofoo');

        $charts = $this->seatsioClient->charts()->lister()->setFilter('foo')->all();
        $chartKeys = \Functional\map($charts, function ($chart) {
            return $chart->key;
        });

        self::assertEquals([$chart3->key, $chart1->key], array_values($chartKeys));
    }

    public function testTag()
    {
        $chart1 = $this->seatsioClient->charts()->create();
        $this->seatsioClient->charts()->addTag($chart1->key, 'foo');

        $chart2 = $this->seatsioClient->charts()->create();

        $chart3 = $this->seatsioClient->charts()->create();
        $this->seatsioClient->charts()->addTag($chart3->key, 'foo');

        $charts = $this->seatsioClient->charts()->lister()->setTag('foo')->all();
        $chartKeys = \Functional\map($charts, function ($chart) {
            return $chart->key;
        });

        self::assertEquals([$chart3->key, $chart1->key], array_values($chartKeys));
    }

    public function testTagAndFilter()
    {
        $chart1 = $this->seatsioClient->charts()->create('bar');
        $this->seatsioClient->charts()->addTag($chart1->key, 'foo');

        $chart2 = $this->seatsioClient->charts()->create();
        $this->seatsioClient->charts()->addTag($chart2->key, 'foo');

        $chart3 = $this->seatsioClient->charts()->create('bar');
        $this->seatsioClient->charts()->addTag($chart3->key, 'foo');

        $chart4 = $this->seatsioClient->charts()->create('bar');

        $charts = $this->seatsioClient->charts()->lister()->setTag('foo')->setFilter('bar')->all();
        $chartKeys = \Functional\map($charts, function ($chart) {
            return $chart->key;
        });

        self::assertEquals([$chart3->key, $chart1->key], array_values($chartKeys));
    }

    public function testExpand()
    {
        $chart = $this->seatsioClient->charts()->create();
        $event1 = $this->seatsioClient->events()->create($chart->key);
        $event2 = $this->seatsioClient->events()->create($chart->key);

        $charts = $this->seatsioClient->charts()->lister()->setExpandEvents()->all();
        $eventIds = \Functional\map($charts->current()->events, function ($event) {
            return $event->id;
        });

        self::assertEquals([$event2->id, $event1->id], array_values($eventIds));
    }

}