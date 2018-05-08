<?php

namespace Seatsio\Charts;

use Seatsio\SeatsioClientTest;

class ListEventsTest extends SeatsioClientTest
{

    public function test()
    {
        $chart = $this->seatsioClient->charts()->create();
        $event1 = $this->seatsioClient->events()->create($chart->key);
        $event2 = $this->seatsioClient->events()->create($chart->key);
        $event3 = $this->seatsioClient->events()->create($chart->key);

        $events = $this->seatsioClient->events()->listAll();
        $eventKeys = \Functional\map($events, function($event) { return $event->key; });

        self::assertEquals([$event3->key, $event2->key, $event1->key], array_values($eventKeys));
    }

}