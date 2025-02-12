<?php

namespace Seatsio\Seasons;

use Seatsio\SeatsioClientTest;

class AddEventsToPartialSeasonTest extends SeatsioClientTest
{
    public function test()
    {
        $chartKey = $this->createTestChart();
        $season = $this->seatsioClient->seasons->create($chartKey, (new SeasonCreationParams())->setEventKeys(['event1', 'event2']));
        $partialSeason = $this->seatsioClient->seasons->createPartialSeason($season->key);

        $updatedPartialSeason = $this->seatsioClient->seasons->addEventsToPartialSeason($season->key, $partialSeason->key, ['event1', 'event2']);

        $eventKeys = array_map(function ($event) {
            return $event->key;
        }, $updatedPartialSeason->events);
        self::assertEquals(['event1', 'event2'], $eventKeys);
        self::assertEquals([$updatedPartialSeason->key], $updatedPartialSeason->events[0]->partialSeasonKeysForEvent);
    }
}
