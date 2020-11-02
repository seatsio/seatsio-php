<?php

namespace Seatsio\Events;

use DateTime;
use Seatsio\Charts\SocialDistancingRuleset;
use Seatsio\SeatsioClientTest;

class CreateEventTest extends SeatsioClientTest
{

    public function testOnlyChartKeyIsRequired()
    {
        $chartKey = $this->createTestChart();

        $event = $this->seatsioClient->events->create($chartKey);

        self::assertNotNull($event->key);
        self::assertNotNull($event->id);
        self::assertEquals($chartKey, $event->chartKey);
        self::assertEquals(TableBookingConfig::inherit(), $event->tableBookingConfig);
        self::assertTrue($event->supportsBestAvailable);
        self::assertNotNull($event->createdOn);
        self::assertNull($event->forSaleConfig);
        self::assertNull($event->updatedOn);
    }

    public function testEventKeyCanBePassedIn()
    {
        $chartKey = $this->createTestChart();

        $event = $this->seatsioClient->events->create($chartKey, 'eventje');

        self::assertEquals('eventje', $event->key);
    }

    public function testTableBookingConfigCanBePassedIn()
    {
        $chartKey = $this->createTestChartWithTables();

        $event = $this->seatsioClient->events->create($chartKey, null, TableBookingConfig::custom(["T1" => "BY_TABLE", "T2" => "BY_SEAT"]));

        self::assertNotNull($event->key);
        self::assertEquals(TableBookingConfig::custom(["T1" => "BY_TABLE", "T2" => "BY_SEAT"]), $event->tableBookingConfig);
    }

    public function testSocialDistancingRulesetKeyCanBePassedIn()
    {
        $chartKey = $this->createTestChartWithTables();
        $this->seatsioClient->charts->saveSocialDistancingRulesets($chartKey, [ "ruleset1" => new SocialDistancingRuleset(0, "My ruleset")]);

        $event = $this->seatsioClient->events->create($chartKey, null, null, "ruleset1");

        self::assertEquals("ruleset1", $event->socialDistancingRulesetKey);
    }

}
