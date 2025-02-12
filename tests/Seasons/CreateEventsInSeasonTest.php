<?php

namespace Seatsio\Seasons;

use Seatsio\SeatsioClientTest;

class CreateEventsInSeasonTest extends SeatsioClientTest
{
    public function testEventKeys()
    {
        $chartKey = $this->createTestChart();
        $season = $this->seatsioClient->seasons->create($chartKey);

        $events = $this->seatsioClient->seasons->createEvents($season->key, ['event1', 'event2']);

        $eventKeys = array_map(function ($event) {
            return $event->key;
        }, $events);
        self::assertEquals(['event2', 'event1'], $eventKeys);
        self::assertEquals(true, $events[0]->isEventInSeason);
        self::assertEquals($season->key, $events[0]->topLevelSeasonKey);
    }

    public function testNumberOfEvents()
    {
        $chartKey = $this->createTestChart();
        $season = $this->seatsioClient->seasons->create($chartKey);

        $events = $this->seatsioClient->seasons->createEvents($season->key, null, 2);

        self::assertCount(2, $events);
    }
}
