<?php

namespace Seatsio\Seasons;

use Seatsio\SeatsioClientTest;
use function Functional\map;

class AddEventsToPartialSeasonTest extends SeatsioClientTest
{
    public function test()
    {
        $chartKey = $this->createTestChart();
        $season = $this->seatsioClient->seasons->create($chartKey, (new SeasonCreationParams())->setEventKeys(['event1', 'event2']));
        $partialSeason = $this->seatsioClient->seasons->createPartialSeason($season->key);

        $updatedPartialSeason = $this->seatsioClient->seasons->addEventsToPartialSeason($season->key, $partialSeason->key, ['event1', 'event2']);

        $eventKeys = map($updatedPartialSeason->events, function ($event) {
            return $event->key;
        });
        self::assertEquals(['event1', 'event2'], $eventKeys);
        self::assertEquals([$updatedPartialSeason->key], $updatedPartialSeason->events[0]->partialSeasonKeysForEvent);
    }
}
