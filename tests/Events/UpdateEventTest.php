<?php

namespace Seatsio\Events;

use Seatsio\Charts\SocialDistancingRuleset;
use Seatsio\SeatsioClientTest;
use stdClass;

class UpdateEventTest extends SeatsioClientTest
{

    public function testUpdateChartKey()
    {
        $chart1 = $this->seatsioClient->charts->create();
        $chart2 = $this->seatsioClient->charts->create();
        $event = $this->seatsioClient->events->create($chart1->key);

        $this->seatsioClient->events->update($event->key, $chart2->key);

        $retrievedEvent = $this->seatsioClient->events->retrieve($event->key);
        self::assertEquals($chart2->key, $retrievedEvent->chartKey);
        self::assertNotNull($retrievedEvent->updatedOn);
    }

    public function testUpdateEventKey()
    {
        $chart = $this->seatsioClient->charts->create();
        $event = $this->seatsioClient->events->create($chart->key);

        $this->seatsioClient->events->update($event->key, null, 'newKey');

        $retrievedEvent = $this->seatsioClient->events->retrieve('newKey');
        self::assertEquals($chart->key, $retrievedEvent->chartKey);
        self::assertEquals('newKey', $retrievedEvent->key);
    }

    public function testUpdateTableBookingConfig()
    {
        $chartKey = $this->createTestChartWithTables();
        $event = $this->seatsioClient->events->create($chartKey);

        $this->seatsioClient->events->update($event->key, null, null, TableBookingConfig::custom(["T1" => "BY_TABLE", "T2" => "BY_SEAT"]));

        $retrievedEvent = $this->seatsioClient->events->retrieve($event->key);
        self::assertEquals($chartKey, $retrievedEvent->chartKey);
        self::assertEquals($event->key, $retrievedEvent->key);
        self::assertEquals(TableBookingConfig::custom(["T1" => "BY_TABLE", "T2" => "BY_SEAT"]), $retrievedEvent->tableBookingConfig);
    }

    public function testUpdateSocialDistancingRulesetKey()
    {
        $chartKey = $this->createTestChartWithTables();
        $this->seatsioClient->charts->saveSocialDistancingRulesets($chartKey, [
            "ruleset1" => SocialDistancingRuleset::ruleBased("My first ruleset")->setIndex(0)->build(),
            "ruleset2" => SocialDistancingRuleset::ruleBased("My second ruleset")->setIndex(1)->build()
        ]);
        $event = $this->seatsioClient->events->create($chartKey, null, null, "ruleset1");

        $this->seatsioClient->events->update($event->key, null, null, null, "ruleset2");

        $retrievedEvent = $this->seatsioClient->events->retrieve($event->key);
        self::assertEquals("ruleset2", $retrievedEvent->socialDistancingRulesetKey);
    }

    public function testRemoveSocialDistancingRulesetKey()
    {
        $chartKey = $this->createTestChartWithTables();
        $this->seatsioClient->charts->saveSocialDistancingRulesets($chartKey, [
            "ruleset1" => SocialDistancingRuleset::ruleBased("My first ruleset")->build()
        ]);
        $event = $this->seatsioClient->events->create($chartKey, null, null, "ruleset1");

        $this->seatsioClient->events->update($event->key, null, null, null, "");

        $retrievedEvent = $this->seatsioClient->events->retrieve($event->key);
        self::assertNull($retrievedEvent->socialDistancingRulesetKey);
    }

    public function testUpdateObjectCategories()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey, null, null, null, ["A-1" => 9]);

        $this->seatsioClient->events->update($event->key, null, null, null, null, ["A-2" => 10]);

        $retrievedEvent = $this->seatsioClient->events->retrieve($event->key);
        self::assertEquals(["A-2" => 10], $retrievedEvent->objectCategories);
    }

    public function testRemoveObjectCategories()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey, null, null, null, ["A-1" => 9]);

        $this->seatsioClient->events->update($event->key, null, null, null, null, new stdClass());

        $retrievedEvent = $this->seatsioClient->events->retrieve($event->key);
        self::assertNull($retrievedEvent->objectCategories);
    }
}
