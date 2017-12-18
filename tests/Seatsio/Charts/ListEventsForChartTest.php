<?php

namespace Seatsio\Charts;

use Seatsio\SeatsioClientTest;

class ListEventsForChartTest extends SeatsioClientTest
{

    public function test()
    {
        $this->seatsioClient->setPageSize(10);
        $chart = $this->seatsioClient->charts()->create();
        $event1 = $this->seatsioClient->events()->create($chart->key);
        $event2 = $this->seatsioClient->events()->create($chart->key);
        $event3 = $this->seatsioClient->events()->create($chart->key);

        $events = $this->seatsioClient->charts()->events($chart->key)->all();
        $eventKeys = \Functional\map($events, function($event) { return $event->key; });

        self::assertEquals([$event3->key, $event2->key, $event1->key], array_values($eventKeys));
    }

}