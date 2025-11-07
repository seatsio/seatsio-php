<?php

namespace Seatsio\Seasons;

use Seatsio\Charts\Category;
use Seatsio\Events\Channel;
use Seatsio\Events\ForSaleConfig;
use Seatsio\Events\TableBookingConfig;
use Seatsio\SeatsioClientTest;

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

    public function testNameCanBePassedIn()
    {
        $chartKey = $this->createTestChart();

        $season = $this->seatsioClient->seasons->create($chartKey, (new SeasonCreationParams())->setName('aSeason'));

        self::assertEquals('aSeason', $season->name);
    }

    public function testEventKeysCanBePassedIn()
    {
        $chartKey = $this->createTestChart();

        $season = $this->seatsioClient->seasons->create($chartKey, (new SeasonCreationParams())->setEventKeys(['event1', 'event2']));

        $eventKeys = array_map(function ($event) {
            return $event->key;
        }, $season->events);
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

    public function testForSalePropagatedFlagCanBePassedIn()
    {
        $chartKey = $this->createTestChart();

        $season = $this->seatsioClient->seasons->create($chartKey, (new SeasonCreationParams())->setForSalePropagated(false));

        self::assertFalse($season->forSalePropagated);
    }

    public function testObjectCategoriesCanBePassedIn()
    {
        $chartKey = $this->createTestChart();

        $season = $this->seatsioClient->seasons->create($chartKey, (new SeasonCreationParams())->setObjectCategories(["A-1" => 10]));

        self::assertEquals(["A-1" => 10], $season->objectCategories);
    }

    public function testCategoriesCanBePassedIn()
    {
        $chartKey = $this->createTestChart();

        $category = new Category("eventCategory", "event-level category", "#AAABBB");
        $categories = [
            $category
        ];
        $season = $this->seatsioClient->seasons->create($chartKey, (new SeasonCreationParams())->setCategories($categories));

        self::assertEquals(4, count($season->categories));
        $eventCategory = current(array_filter($season->categories, function ($category) {
            return $category->key == 'eventCategory';
        }));
        self::assertNotNull($eventCategory);
        self::assertEquals('eventCategory', $eventCategory->key);
        self::assertEquals('event-level category', $eventCategory->label);
        self::assertEquals('#AAABBB', $eventCategory->color);
        self::assertEquals(false, $eventCategory->accessible);
    }

}
