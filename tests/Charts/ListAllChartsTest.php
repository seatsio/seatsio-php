<?php

namespace Seatsio\Charts;

use Seatsio\SeatsioClientTest;

class ListAllChartsTest extends SeatsioClientTest
{

    public function test()
    {
        $chart1 = $this->seatsioClient->charts->create();
        $chart2 = $this->seatsioClient->charts->create();
        $chart3 = $this->seatsioClient->charts->create();

        $charts = $this->seatsioClient->charts->listAll();
        $chartKeys = array_map(function ($chart) {
            return $chart->key;
        }, iterator_to_array($charts));

        self::assertEquals([$chart3->key, $chart2->key, $chart1->key], array_values($chartKeys));
    }

    public function testNoCharts()
    {
        $charts = $this->seatsioClient->charts->listAll();

        self::assertFalse($charts->valid());
    }

    public function testFilter()
    {
        $chart1 = $this->seatsioClient->charts->create('foo');
        $chart2 = $this->seatsioClient->charts->create('bar');
        $chart3 = $this->seatsioClient->charts->create('foofoo');

        $charts = $this->seatsioClient->charts->listAll((new ChartListParams())->withFilter('foo'));
        $chartKeys = array_map(function ($chart) {
            return $chart->key;
        }, iterator_to_array($charts));

        self::assertEquals([$chart3->key, $chart1->key], array_values($chartKeys));
    }

    public function testTag()
    {
        $chart1 = $this->seatsioClient->charts->create();
        $this->seatsioClient->charts->addTag($chart1->key, 'foo');

        $chart2 = $this->seatsioClient->charts->create();

        $chart3 = $this->seatsioClient->charts->create();
        $this->seatsioClient->charts->addTag($chart3->key, 'foo');

        $charts = $this->seatsioClient->charts->listAll((new ChartListParams())->withTag('foo'));
        $chartKeys = array_map(function ($chart) {
            return $chart->key;
        }, iterator_to_array($charts));

        self::assertEquals([$chart3->key, $chart1->key], array_values($chartKeys));
    }

    public function testTagAndFilter()
    {
        $chart1 = $this->seatsioClient->charts->create('bar');
        $this->seatsioClient->charts->addTag($chart1->key, 'foo');

        $chart2 = $this->seatsioClient->charts->create();
        $this->seatsioClient->charts->addTag($chart2->key, 'foo');

        $chart3 = $this->seatsioClient->charts->create('bar');
        $this->seatsioClient->charts->addTag($chart3->key, 'foo');

        $chart4 = $this->seatsioClient->charts->create('bar');

        $charts = $this->seatsioClient->charts->listAll((new ChartListParams())->withFilter('bar')->withTag('foo'));
        $chartKeys = array_map(function ($chart) {
            return $chart->key;
        }, iterator_to_array($charts));

        self::assertEquals([$chart3->key, $chart1->key], array_values($chartKeys));
    }

    public function testAllFieldsExpanded()
    {
        $chart = $this->createTestChartWithZones();
        $event1 = $this->seatsioClient->events->create($chart);
        $event2 = $this->seatsioClient->events->create($chart);

        $params = (new ChartListParams())
            ->withExpandEvents(true)
            ->withExpandValidation(true)
            ->withExpandVenueType(true)
            ->withExpandZones(true);
        $charts = $this->seatsioClient->charts->listAll($params);

        $eventIds = array_map(function ($event) {
            return $event->id;
        }, $charts->current()->events);
        self::assertEquals([$event2->id, $event1->id], array_values($eventIds));
        self::assertEquals("WITH_ZONES", $charts->current()->venueType);
        self::assertEquals([new Zone("finishline", "Finish Line"), new Zone("midtrack", "Mid Track")], $charts->current()->zones);
        self::assertNotNull($charts->current()->validation);
    }

    public function testNoFieldsExpanded()
    {
        $this->createTestChartWithZones();

        $charts = $this->seatsioClient->charts->listAll((new ChartListParams())->withExpandEvents(true));
        self::assertEmpty($charts->current()->events);
        self::assertNull($charts->current()->venueType);
        self::assertNull($charts->current()->validation);
        self::assertNull($charts->current()->zones);
    }

    public function testWithValidation()
    {
        self::createTestChartWithErrors();

        $charts = $this->seatsioClient->charts->listAll((new ChartListParams())->withValidation(true));

        self::assertEquals(["errors" => [], "warnings" => []], $charts->current()->validation);
    }

    public function testWithoutValidation()
    {
        $this->seatsioClient->charts->create();

        $charts = $this->seatsioClient->charts->listAll((new ChartListParams()));

        self::assertNull($charts->current()->validation);
    }

}
