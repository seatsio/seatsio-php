<?php

namespace Seatsio\Seasons;

use Seatsio\SeatsioClientTest;

class RemoveEventFromPartialSeasonTest extends SeatsioClientTest
{
    public function test()
    {
        $chartKey = $this->createTestChart();
        $season = $this->seatsioClient->seasons->create($chartKey, (new SeasonCreationParams())->setEventKeys(['event1', 'event2']));
        $partialSeason = $this->seatsioClient->seasons->createPartialSeason($season->key, null, ['event1', 'event2']);

        $updatedPartialSeason = $this->seatsioClient->seasons->removeEventFromPartialSeason($season->key, $partialSeason->key, 'event2');

        $eventKeys = array_map(function ($event) {
            return $event->key;
        }, $updatedPartialSeason->events);
        self::assertEquals(['event1'], $eventKeys);
    }
}
