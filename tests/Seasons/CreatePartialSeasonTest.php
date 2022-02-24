<?php

namespace Seatsio\Seasons;

use Seatsio\SeatsioClientTest;
use function Functional\map;

class CreatePartialSeasonTest extends SeatsioClientTest
{
    public function test()
    {
        $chartKey = $this->createTestChart();
        $season = $this->seatsioClient->seasons->create($chartKey);

        $partialSeason = $this->seatsioClient->seasons->createPartialSeason($season->key);

        self::assertNotNull($partialSeason->key);
        self::assertEquals(true, $partialSeason->isPartialSeason);
        self::assertEquals($season->key, $partialSeason->topLevelSeasonKey);
    }

    public function testPartialSeasonKeyCanBePassedIn()
    {
        $chartKey = $this->createTestChart();
        $season = $this->seatsioClient->seasons->create($chartKey);

        $partialSeason = $this->seatsioClient->seasons->createPartialSeason($season->key, 'aPartialSeason');

        self::assertEquals('aPartialSeason', $partialSeason->key);
    }

    public function testEventKeysCanBePassedIn()
    {
        $chartKey = $this->createTestChart();
        $season = $this->seatsioClient->seasons->create($chartKey, (new SeasonCreationParams())->setEventKeys(['event1', 'event2']));

        $partialSeason = $this->seatsioClient->seasons->createPartialSeason($season->key, null, ['event1', 'event2']);

        $eventKeys = map($partialSeason->events, function ($event) {
            return $event->key;
        });
        self::assertEquals(['event1', 'event2'], $eventKeys);
    }
}
