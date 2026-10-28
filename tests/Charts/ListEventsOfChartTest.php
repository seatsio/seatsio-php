<?php

namespace Seatsio\Charts;

use Seatsio\SeatsioClientTest;

class ListEventsOfChartTest extends SeatsioClientTest
{

    public function testAll()
    {
        $chart = $this->seatsioClient->charts->create();
        $event1 = $this->seatsioClient->events->create($chart->key);
        $event2 = $this->seatsioClient->events->create($chart->key);
        $otherChart = $this->seatsioClient->charts->create();
        $this->seatsioClient->events->create($otherChart->key);

        $events = $this->seatsioClient->charts->events($chart->key)->all();
        $eventKeys = array_map(function ($event) {
            return $event->key;
        }, iterator_to_array($events));

        self::assertEquals([$event2->key, $event1->key], array_values($eventKeys));
    }

    public function testFirstPageAndPageAfter()
    {
        $chart = $this->seatsioClient->charts->create();
        $event1 = $this->seatsioClient->events->create($chart->key);
        $event2 = $this->seatsioClient->events->create($chart->key);
        $event3 = $this->seatsioClient->events->create($chart->key);

        $firstPage = $this->seatsioClient->charts->events($chart->key)->firstPage(2);
        $firstPageKeys = array_map(function ($event) {
            return $event->key;
        }, $firstPage->items);
        self::assertEquals([$event3->key, $event2->key], array_values($firstPageKeys));
        self::assertEquals($event2->id, $firstPage->nextPageStartsAfter);

        $pageAfter = $this->seatsioClient->charts->events($chart->key)->pageAfter($firstPage->nextPageStartsAfter, 2);
        $pageAfterKeys = array_map(function ($event) {
            return $event->key;
        }, $pageAfter->items);
        self::assertEquals([$event1->key], array_values($pageAfterKeys));
    }

}
