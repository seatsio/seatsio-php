<?php

namespace Seatsio\Events;

use Seatsio\Charts\SocialDistancingRuleset;
use Seatsio\Common\IDs;
use Seatsio\SeatsioClientTest;
use Seatsio\SeatsioException;

class ChangeObjectStatusTest extends SeatsioClientTest
{

    public function test()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey);

        $result = $this->seatsioClient->events->changeObjectStatus($event->key, "A-1", "lolzor");

        $objectDetails = $result->objects["A-1"];
        self::assertEquals("lolzor", $objectDetails->status);
        self::assertEquals("A-1", $objectDetails->label);
        self::assertEquals(someLabels("1", "seat", "A", "row"), $objectDetails->labels);
        self::assertEquals(new IDs("1", "A", null), $objectDetails->ids);
        self::assertEquals("Cat1", $objectDetails->categoryLabel);
        self::assertEquals(9, $objectDetails->categoryKey);
        self::assertNull($objectDetails->ticketType);
        self::assertNull($objectDetails->orderId);
        self::assertEquals("seat", $objectDetails->objectType);
        self::assertTrue($objectDetails->forSale);
        self::assertNull($objectDetails->section);
        self::assertNull($objectDetails->entrance);
        self::assertNull($objectDetails->leftNeighbour);
        self::assertEquals("A-2", $objectDetails->rightNeighbour);
    }

    public function testTableSeat()
    {
        $chartKey = $this->createTestChartWithTables();
        $event = $this->seatsioClient->events->create($chartKey);

        $result = $this->seatsioClient->events->changeObjectStatus($event->key, "T1-1", "lolzor");

        self::assertEquals(["T1-1"], array_keys($result->objects));
    }

    public function testTable()
    {
        $chartKey = $this->createTestChartWithTables();
        $event = $this->seatsioClient->events->create($chartKey, CreateEventParams::create()->setTableBookingConfig(TableBookingConfig::allByTable()));

        $result = $this->seatsioClient->events->changeObjectStatus($event->key, "T1", "lolzor");

        self::assertEquals(["T1"], array_keys($result->objects));
    }

    public function testGA()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey);

        $result = $this->seatsioClient->events->changeObjectStatus($event->key, "GA2", "lolzor");

        self::assertEquals(["GA2"], array_keys($result->objects));
    }

    public function testObjectIdAsString()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey);

        $this->seatsioClient->events->changeObjectStatus($event->key, "A-1", "lolzor");

        $objectInfo = $this->seatsioClient->events->retrieveObjectInfo($event->key, "A-1");
        self::assertEquals("lolzor", $objectInfo->status);
    }

    public function testObjectIdInsideObject()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey);

        $this->seatsioClient->events->changeObjectStatus($event->key, new ObjectProperties("A-1"), "lolzor");

        $objectInfo = $this->seatsioClient->events->retrieveObjectInfo($event->key, "A-1");
        self::assertEquals("lolzor", $objectInfo->status);
    }

    public function testHoldToken()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey);
        $holdToken = $this->seatsioClient->holdTokens->create();

        $this->seatsioClient->events->changeObjectStatus($event->key, "A-1", EventObjectInfo::$HELD, $holdToken->holdToken);

        $objectInfo = $this->seatsioClient->events->retrieveObjectInfo($event->key, "A-1");
        self::assertEquals(EventObjectInfo::$HELD, $objectInfo->status);
        self::assertEquals($holdToken->holdToken, $objectInfo->holdToken);
    }

    public function testOrderId()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey);

        $this->seatsioClient->events->changeObjectStatus($event->key, "A-1", "lolzor", null, "order1");

        $objectInfo = $this->seatsioClient->events->retrieveObjectInfo($event->key, "A-1");
        self::assertEquals("order1", $objectInfo->orderId);
    }

    public function testKeepExtraData()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey);
        $extraData = ["foo" => "bar"];
        $this->seatsioClient->events->updateExtraData($event->key, "A-1", $extraData);

        $this->seatsioClient->events->changeObjectStatus($event->key, "A-1", "lolzor", null, null, true);

        $objectInfo = $this->seatsioClient->events->retrieveObjectInfo($event->key, "A-1");
        self::assertEquals((object)$extraData, $objectInfo->extraData);
    }

    public function testChannelKeys()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey);
        $this->seatsioClient->events->channels->replace($event->key, [
            new Channel("channelKey1", "channel 1", "#FF0000", 1, ["A-1", "A-2"])
        ]);

        $this->seatsioClient->events->changeObjectStatus($event->key, "A-1", "someStatus", null, null, null, null, ["channelKey1"]);

        $objectInfo = $this->seatsioClient->events->retrieveObjectInfo($event->key, "A-1");
        self::assertEquals("someStatus", $objectInfo->status);
    }

    public function testIgnoreChannels()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey);
        $this->seatsioClient->events->channels->replace($event->key, [
            new Channel("channelKey1", "channel 1", "#FF0000", 1, ["A-1", "A-2"])
        ]);

        $this->seatsioClient->events->changeObjectStatus($event->key, "A-1", "someStatus", null, null, null, true);

        $objectInfo = $this->seatsioClient->events->retrieveObjectInfo($event->key, "A-1");
        self::assertEquals("someStatus", $objectInfo->status);
    }

    public function testIgnoreSocialDistancing()
    {
        $chartKey = $this->createTestChart();
        $ruleset = SocialDistancingRuleset::fixed("ruleset")->setDisabledSeats(["A-1"])->build();
        $this->seatsioClient->charts->saveSocialDistancingRulesets($chartKey, ["ruleset" => $ruleset]);
        $event = $this->seatsioClient->events->create($chartKey, CreateEventParams::create()->setSocialDistancingRulesetKey("ruleset"));

        $this->seatsioClient->events->changeObjectStatus($event->key, "A-1", "someStatus", null, null, null, null, null, true);

        $objectInfo = $this->seatsioClient->events->retrieveObjectInfo($event->key, "A-1");
        self::assertEquals("someStatus", $objectInfo->status);
    }

    public function testAllowedPreviousStatuses()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey);

        try {
            $this->seatsioClient->events->changeObjectStatus(
                $event->key,
                    "A-1",
                    "lolzor",
                    null,
                    null,
                    null,
                    true,
                    null,
                    true,
                    ['someOtherStatus']
            );
            throw new \Exception("Should have failed");
        } catch (SeatsioException $exception) {
            self::assertEquals(1, sizeof($exception->errors));
            self::assertEquals("ILLEGAL_STATUS_CHANGE", $exception->errors[0]->code);
        }
    }

    public function testRejectedPreviousStatuses()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey);

        try {
            $this->seatsioClient->events->changeObjectStatus(
                $event->key,
                "A-1",
                "lolzor",
                null,
                null,
                null,
                true,
                null,
                true,
                null,
                ['free']
            );
            throw new \Exception("Should have failed");
        } catch (SeatsioException $exception) {
            self::assertEquals(1, sizeof($exception->errors));
            self::assertEquals("ILLEGAL_STATUS_CHANGE", $exception->errors[0]->code);
        }
    }
}
