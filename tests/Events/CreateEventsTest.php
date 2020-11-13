<?php

namespace Seatsio\Events;

use Seatsio\Charts\SocialDistancingRuleset;
use Seatsio\SeatsioClientTest;

class CreateEventsTest extends SeatsioClientTest
{

    public function test_multipleEvents()
    {
        $chartKey = $this->createTestChart();
        $params = [new EventCreationParams(), new EventCreationParams()];

        $events = $this->seatsioClient->events->createMultiple($chartKey, $params);

        self::assertEquals(2, sizeof($events));
        foreach ($events as $e) {
            self::assertNotNull($e->key);
        }
    }

    public function test_single_event()
    {
        $chartKey = $this->createTestChart();
        $params = [new EventCreationParams()];

        $events = $this->seatsioClient->events->createMultiple($chartKey, $params);

        self::assertEquals(1, sizeof($events));
        $event = $events[0];
        self::assertNotEquals(0, $event->id);
        self::assertNotNull($event->id);
        self::assertNotNull($event->key);
        self::assertEquals($chartKey, $event->chartKey);
        self::assertEquals(TableBookingConfig::inherit(), $event->tableBookingConfig);
        self::assertNull($event->supportsBestAvailable);
        self::assertNull($event->forSaleConfig);
        self::assertNull($event->updatedOn);
    }

    public function test_event_key_can_be_passed_in()
    {
        $chartKey = $this->createTestChart();
        $params = [new EventCreationParams("event1"), new EventCreationParams("event2")];

        $events = $this->seatsioClient->events->createMultiple($chartKey, $params);

        self::assertEquals(2, sizeof($events));
        self::assertEquals("event1", $events[0]->key);
        self::assertEquals("event2", $events[1]->key);
    }

    public function test_tableBookingConfigCanBePassedIn()
    {
        $chartKey = $this->createTestChartWithTables();
        $params = [
            (new EventCreationParams())->setTableBookingConfig(TableBookingConfig::custom(["T1" => "BY_TABLE", "T2" => "BY_SEAT"])),
            (new EventCreationParams())->setTableBookingConfig(TableBookingConfig::custom(["T1" => "BY_SEAT", "T2" => "BY_TABLE"]))
        ];

        $events = $this->seatsioClient->events->createMultiple($chartKey, $params);

        self::assertEquals(2, sizeof($events));
        self::assertEquals(TableBookingConfig::custom(["T1" => "BY_TABLE", "T2" => "BY_SEAT"]), $events[0]->tableBookingConfig);
        self::assertEquals(TableBookingConfig::custom(["T1" => "BY_SEAT", "T2" => "BY_TABLE"]), $events[1]->tableBookingConfig);
    }

    public function test_socialDistancingRulesetKeyCanBePassedIn()
    {
        $chartKey = $this->createTestChartWithTables();
        $this->seatsioClient->charts->saveSocialDistancingRulesets($chartKey, ["ruleset1" => SocialDistancingRuleset::ruleBased("My ruleset")->build()]);
        $params = [(new EventCreationParams())->setSocialDistancingRulesetKey("ruleset1"), (new EventCreationParams())->setSocialDistancingRulesetKey("ruleset1")];

        $events = $this->seatsioClient->events->createMultiple($chartKey, $params);

        self::assertEquals(2, sizeof($events));
        self::assertEquals("ruleset1", $events[0]->socialDistancingRulesetKey);
        self::assertEquals("ruleset1", $events[1]->socialDistancingRulesetKey);
    }

}
