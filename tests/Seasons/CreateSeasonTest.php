<?php

namespace Seatsio\Seasons;

use Seatsio\Charts\SocialDistancingRuleset;
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
        self::assertNotNull($season->id);
        $seasonEvent = $season->seasonEvent;
        self::assertEquals($seasonEvent->key, $season->key);
        self::assertNotNull($seasonEvent->id);
        self::assertEquals($chartKey, $seasonEvent->chartKey);
        self::assertEquals(TableBookingConfig::inherit(), $seasonEvent->tableBookingConfig);
        self::assertTrue($seasonEvent->supportsBestAvailable);
        self::assertNotNull($seasonEvent->createdOn);
        self::assertNull($seasonEvent->forSaleConfig);
        self::assertNull($seasonEvent->updatedOn);
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
        self::assertEquals(TableBookingConfig::custom(["T1" => "BY_TABLE", "T2" => "BY_SEAT"]), $season->seasonEvent->tableBookingConfig);
    }

    public function testTableBookingConfigInheritCanBePassedIn()
    {
        $chartKey = $this->createTestChartWithTables();

        $season = $this->seatsioClient->seasons->create($chartKey, (new SeasonCreationParams())->setTableBookingConfig(TableBookingConfig::inherit()));

        self::assertNotNull($season->key);
        self::assertEquals(TableBookingConfig::inherit(), $season->seasonEvent->tableBookingConfig);
    }

    public function testSocialDistancingRulesetKeyCanBePassedIn()
    {
        $chartKey = $this->createTestChartWithTables();
        $this->seatsioClient->charts->saveSocialDistancingRulesets($chartKey, ["ruleset1" => SocialDistancingRuleset::ruleBased("My ruleset")->build()]);

        $event = $this->seatsioClient->events->create($chartKey, null, null, "ruleset1");

        self::assertEquals("ruleset1", $event->socialDistancingRulesetKey);
    }

}
