<?php

namespace Seatsio\Seasons;

use Seatsio\Events\Channel;
use Seatsio\Events\ForSaleConfig;
use Seatsio\Events\TableBookingConfig;
use Seatsio\SeatsioClientTest;
use function Functional\map;

class CreateSeasonTest extends SeatsioClientTest
{
    public function testOnlyChartKeyIsRequired()
    {
        $chartKey = $this->createTestChart();

        $season = $this->seatsioClient->seasons->create($chartKey);

        self::assertNotNull($season->key);
        self::assertEquals(true, $season->isTopLevelSeason);
        self::assertNull($season->topLevelSeasonKey);
        self::assertNotNull($season->id);
        self::assertEquals($chartKey, $season->chartKey);
        self::assertEquals(TableBookingConfig::inherit(), $season->tableBookingConfig);
        self::assertTrue($season->supportsBestAvailable);
        self::assertNotNull($season->createdOn);
        self::assertNull($season->forSaleConfig);
        self::assertNull($season->updatedOn);
    }

    public function testKeyCanBePassedIn()
    {
        $chartKey = $this->createTestChart();

        $season = $this->seatsioClient->seasons->create($chartKey, new SeasonCreationParams('aSeason'));

        self::assertEquals('aSeason', $season->key);
    }

    public function testEventKeysCanBePassedIn()
    {
        $chartKey = $this->createTestChart();

        $season = $this->seatsioClient->seasons->create($chartKey, (new SeasonCreationParams())->setEventKeys(['event1', 'event2']));

        $eventKeys = map($season->events, function ($event) {
            return $event->key;
        });
        self::assertEquals(['event1', 'event2'], $eventKeys);
    }

    public function testNumberOfEventsCanBePassedIn()
    {
        $chartKey = $this->createTestChart();

        $season = $this->seatsioClient->seasons->create($chartKey, (new SeasonCreationParams())->setNumberOfEvents(2));

        self::assertCount(2, $season->events);
    }

    public function testTableBookingConfigCustomCanBePassedIn()
    {
        $chartKey = $this->createTestChartWithTables();

        $season = $this->seatsioClient->seasons->create($chartKey, (new SeasonCreationParams())->setTableBookingConfig(TableBookingConfig::custom(["T1" => "BY_TABLE", "T2" => "BY_SEAT"])));

        self::assertNotNull($season->key);
        self::assertEquals(TableBookingConfig::custom(["T1" => "BY_TABLE", "T2" => "BY_SEAT"]), $season->tableBookingConfig);
    }

    public function testTableBookingConfigInheritCanBePassedIn()
    {
        $chartKey = $this->createTestChartWithTables();

        $season = $this->seatsioClient->seasons->create($chartKey, (new SeasonCreationParams())->setTableBookingConfig(TableBookingConfig::inherit()));

        self::assertNotNull($season->key);
        self::assertEquals(TableBookingConfig::inherit(), $season->tableBookingConfig);
    }

    public function testChannelsCanBePassedIn()
    {
        $chartKey = $this->createTestChart();
        $channels = [
            new Channel("channelKey1", "channel 1", "#FF0000", 1, ["A-1", "A-2"]),
            new Channel("channelKey2", "channel 2", "#00FFFF", 2, [])
        ];

        $season = $this->seatsioClient->seasons->create($chartKey, (new SeasonCreationParams())->setChannels($channels));

        self::assertNotNull($season->key);
        self::assertEquals($channels, $season->channels);
    }

    public function testForSaleConfigCanBePassedIn()
    {
        $chartKey = $this->createTestChart();
        $forSaleConfig = new ForSaleConfig(false, ["A-1"], ["GA1" => 3], ["Cat1"]);

        $season = $this->seatsioClient->seasons->create($chartKey, (new SeasonCreationParams())->setForSaleConfig($forSaleConfig));

        self::assertNotNull($season->key);
        self::assertEquals($forSaleConfig, $season->forSaleConfig);
    }
}
