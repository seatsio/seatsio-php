<?php

namespace Seatsio\Seasons;

use Seatsio\SeatsioClientTest;
use function Functional\map;

class CreateEventsInSeasonTest extends SeatsioClientTest
{
    public function eventKeys()
    {
        $chartKey = $this->createTestChart();
        $season = $this->seatsioClient->seasons->create($chartKey);

        $updatedSeason = $this->seatsioClient->seasons->createEvents($season->key, ['event1', 'event2']);

        $eventKeys = map($updatedSeason->events, function ($event) {
            return $event->key;
        });
        self::assertEquals(['event1', 'event2'], $eventKeys);
    }

    public function numberOfEvents()
    {
        $chartKey = $this->createTestChart();
        $season = $this->seatsioClient->seasons->create($chartKey);

        $updatedSeason = $this->seatsioClient->seasons->createEvents($season->key, null, 2);

        self::assertCount(2, $updatedSeason->events);
    }
}
