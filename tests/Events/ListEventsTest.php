<?php

namespace Seatsio\Charts;

use Seatsio\Seasons\SeasonCreationParams;
use Seatsio\SeatsioClientTest;

class ListEventsTest extends SeatsioClientTest
{

    public function test()
    {
        $chart = $this->seatsioClient->charts->create();
        $event1 = $this->seatsioClient->events->create($chart->key);
        $event2 = $this->seatsioClient->events->create($chart->key);
        $event3 = $this->seatsioClient->events->create($chart->key);

        $events = $this->seatsioClient->events->listAll();
        $eventKeys = array_map(function ($event) {
            return $event->key;
        }, iterator_to_array($events));

        self::assertEquals([$event3->key, $event2->key, $event1->key], array_values($eventKeys));
    }

    public function testListSeasons()
    {
        $chartKey = $this->createTestChart();
        $this->seatsioClient->seasons->create($chartKey, new SeasonCreationParams('season1'));
        $this->seatsioClient->seasons->create($chartKey, new SeasonCreationParams('season2'));

        $seasonsAndEvents = $this->seatsioClient->events->listAll();
        $areSeasons = array_map(function ($season) {
            return $season->isSeason();
        }, iterator_to_array($seasonsAndEvents));

        self::assertEquals([true, true], array_values($areSeasons));
    }
}
