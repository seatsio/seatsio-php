<?php

namespace Seatsio\Seasons;

use Seatsio\SeatsioClientTest;
use function Functional\map;

class CreateEventsInSeasonTest extends SeatsioClientTest
{
    public function testEventKeys()
    {
        $chartKey = $this->createTestChart();
        $season = $this->seatsioClient->seasons->create($chartKey);

        $updatedSeason = $this->seatsioClient->seasons->createEvents($season->key, ['event1', 'event2']);

        $eventKeys = map($updatedSeason->events, function ($event) {
            return $event->key;
        });
        self::assertEquals(['event2', 'event1'], $eventKeys);
        self::assertEquals(true, $updatedSeason->events[0]->isEventInSeason);
        self::assertEquals($season->key, $updatedSeason->events[0]->topLevelSeasonKey);
    }

    public function testNumberOfEvents()
    {
        $chartKey = $this->createTestChart();
        $season = $this->seatsioClient->seasons->create($chartKey);

        $updatedSeason = $this->seatsioClient->seasons->createEvents($season->key, null, 2);

        self::assertCount(2, $updatedSeason->events);
    }
}
