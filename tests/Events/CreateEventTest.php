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
        self::assertFalse($event->bookWholeTables);
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

    public function testBookWholeTablesCanBePassedIn()
    {
        $chartKey = $this->createTestChart();

        $event = $this->seatsioClient->events->create($chartKey, null, false);

        self::assertNotNull($event->key);
        self::assertFalse($event->bookWholeTables);
    }

    public function testTableBookingModesCanBePassedIn()
    {
        $chartKey = $this->createTestChartWithTables();

        $event = $this->seatsioClient->events->create($chartKey, null, ["T1" => "BY_TABLE", "T2" => "BY_SEAT"]);

        self::assertNotNull($event->key);
        self::assertFalse($event->bookWholeTables);
        self::assertEquals((object)["T1" => "BY_TABLE", "T2" => "BY_SEAT"], $event->tableBookingModes);
    }

    public function testSocialDistancingRulesetKeyCanBePassedIn()
    {
        $chartKey = $this->createTestChartWithTables();
        $this->seatsioClient->charts->saveSocialDistancingRulesets($chartKey, [ "ruleset1" => new SocialDistancingRuleset(0, "My ruleset")]);

        $event = $this->seatsioClient->events->create($chartKey, null, null, "ruleset1");

        self::assertEquals("ruleset1", $event->socialDistancingRulesetKey);
    }

}
