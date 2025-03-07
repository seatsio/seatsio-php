<?php

namespace Seatsio\Seasons;

use Seatsio\SeatsioClientTest;

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

    public function testNameCanBePassedIn()
    {
        $chartKey = $this->createTestChart();
        $season = $this->seatsioClient->seasons->create($chartKey);

        $partialSeason = $this->seatsioClient->seasons->createPartialSeason($season->key, null, 'aPartialSeason');

        self::assertEquals('aPartialSeason', $partialSeason->name);
    }

    public function testEventKeysCanBePassedIn()
    {
        $chartKey = $this->createTestChart();
        $season = $this->seatsioClient->seasons->create($chartKey, (new SeasonCreationParams())->setEventKeys(['event1', 'event2']));

        $partialSeason = $this->seatsioClient->seasons->createPartialSeason($season->key, null, null, ['event1', 'event2']);

        $eventKeys = array_map(function ($event) {
            return $event->key;
        }, $partialSeason->events);
        self::assertEquals(['event1', 'event2'], $eventKeys);
        self::assertEquals([$partialSeason->key], $partialSeason->events[0]->partialSeasonKeysForEvent);
    }
}
