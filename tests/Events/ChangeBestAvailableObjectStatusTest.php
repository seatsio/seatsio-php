<?php

namespace Seatsio\Events;

use Seatsio\Common\IDs;
use Seatsio\SeatsioClientTest;

class ChangeBestAvailableObjectStatusTest extends SeatsioClientTest
{

    public function test()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey);

        $bestAvailableObjects = $this->seatsioClient->events->changeBestAvailableObjectStatus($event->key, 2, "lolzor");

        self::assertTrue($bestAvailableObjects->nextToEachOther);
        self::assertEquals(["B-4", "B-5"], $bestAvailableObjects->objects, '', 0.0, 10, true);
    }

    public function testObjectDetails()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey);

        $bestAvailableObjects = $this->seatsioClient->events->changeBestAvailableObjectStatus($event->key, 1, "lolzor");

        $objectDetails = $bestAvailableObjects->objectDetails["B-5"];
        self::assertEquals("lolzor", $objectDetails->status);
        self::assertEquals("B-5", $objectDetails->label);
        self::assertEquals(someLabels("5", "seat", "B", "row"), $objectDetails->labels);
        self::assertEquals(new IDs("5", "B", null), $objectDetails->ids);
        self::assertEquals("Cat1", $objectDetails->categoryLabel);
        self::assertEquals(9, $objectDetails->categoryKey);
        self::assertNull($objectDetails->ticketType);
        self::assertNull($objectDetails->orderId);
        self::assertEquals("seat", $objectDetails->objectType);
        self::assertTrue($objectDetails->forSale);
        self::assertNull($objectDetails->section);
        self::assertNull($objectDetails->entrance);
        self::assertEquals("B-4", $objectDetails->leftNeighbour);
        self::assertEquals("B-6", $objectDetails->rightNeighbour);
    }

    public function testCategories()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey);

        $bestAvailableObjects = $this->seatsioClient->events->changeBestAvailableObjectStatus($event->key, 3, "lolzor", ["cat2"]);

        self::assertEquals(["C-4", "C-5", "C-6"], $bestAvailableObjects->objects, '', 0.0, 10, true);
    }

    public function testExtraData()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey);
        $extraData = [
            ["foo" => "bar"],
            ["foo" => "baz"]
        ];

        $bestAvailableObjects = $this->seatsioClient->events->changeBestAvailableObjectStatus($event->key, 2, "lolzor", null, null, $extraData);

        self::assertEquals(["B-4", "B-5"], $bestAvailableObjects->objects, '', 0.0, 10, true);
        $b4ObjectInfo = $this->seatsioClient->events->retrieveObjectInfo($event->key, "B-4");
        self::assertEquals($b4ObjectInfo->extraData, (object)["foo" => "bar"]);
        $b5ObjectInfo = $this->seatsioClient->events->retrieveObjectInfo($event->key, "B-5");
        self::assertEquals($b5ObjectInfo->extraData, (object)["foo" => "baz"]);
    }

    public function testTicketTypes()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey);

        $bestAvailableObjects = $this->seatsioClient->events->changeBestAvailableObjectStatus($event->key, 2, "lolzor", null, null, null, ["adult", "child"]);

        self::assertEquals(["B-4", "B-5"], $bestAvailableObjects->objects, '', 0.0, 10, true);
        $b4ObjectInfo = $this->seatsioClient->events->retrieveObjectInfo($event->key, "B-4");
        self::assertEquals($b4ObjectInfo->ticketType, "adult");
        $b5ObjectInfo = $this->seatsioClient->events->retrieveObjectInfo($event->key, "B-5");
        self::assertEquals($b5ObjectInfo->ticketType, "child");
    }

    public function testHoldToken()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey);
        $holdToken = $this->seatsioClient->holdTokens->create();

        $bestAvailableObjects = $this->seatsioClient->events->changeBestAvailableObjectStatus($event->key, 1, ObjectInfo::$HELD, null, $holdToken->holdToken);

        $objectInfo = $this->seatsioClient->events->retrieveObjectInfo($event->key, $bestAvailableObjects->objects[0]);
        self::assertEquals(ObjectInfo::$HELD, $objectInfo->status);
        self::assertEquals($holdToken->holdToken, $objectInfo->holdToken);
    }

    public function testOrderId()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey);

        $bestAvailableObjects = $this->seatsioClient->events->changeBestAvailableObjectStatus($event->key, 1, "lolzor", null, null, null, null, "anOrder");

        $objectInfo = $this->seatsioClient->events->retrieveObjectInfo($event->key, $bestAvailableObjects->objects[0]);
        self::assertEquals("anOrder", $objectInfo->orderId);
    }

    public function testBook()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey);

        $bestAvailableObjects = $this->seatsioClient->events->bookBestAvailable($event->key, 3);

        self::assertTrue($bestAvailableObjects->nextToEachOther);
        self::assertEquals(["B-4", "B-5", "B-6"], $bestAvailableObjects->objects, '', 0.0, 10, true);
    }

    public function testHold()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey);
        $holdToken = $this->seatsioClient->holdTokens->create();

        $bestAvailableObjects = $this->seatsioClient->events->holdBestAvailable($event->key, 1, $holdToken->holdToken);

        $objectInfo = $this->seatsioClient->events->retrieveObjectInfo($event->key, $bestAvailableObjects->objects[0]);
        self::assertEquals(ObjectInfo::$HELD, $objectInfo->status);
        self::assertEquals($holdToken->holdToken, $objectInfo->holdToken);
    }

    public function testKeepExtraData()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey);
        $extraData = ["foo" => "bar"];
        $this->seatsioClient->events->updateExtraData($event->key, "B-5", $extraData);

        $this->seatsioClient->events->changeBestAvailableObjectStatus($event->key, 1, "lolzor", null, null, null, null, null, true);

        $objectInfo = $this->seatsioClient->events->retrieveObjectInfo($event->key, "B-5");
        self::assertEquals((object)$extraData, $objectInfo->extraData);
    }

    public function testChannelKeys()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey);
        $this->seatsioClient->events->updateChannels($event->key, [
            "channelKey1" => new Channel("channel 1", "#FF0000", 1)
        ]);
        $this->seatsioClient->events->assignObjectsToChannels($event->key, [
            "channelKey1" => ["B-6"]
        ]);

        $bestAvailableObjects = $this->seatsioClient->events->changeBestAvailableObjectStatus($event->key, 1, "lolzor", null, null, null, null, null, null, null, ["channelKey1"]);

        self::assertEquals(["B-6"], $bestAvailableObjects->objects);
    }

    public function testIgnoreChannels()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey);
        $this->seatsioClient->events->updateChannels($event->key, [
            "channelKey1" => new Channel("channel 1", "#FF0000", 1)
        ]);
        $this->seatsioClient->events->assignObjectsToChannels($event->key, [
            "channelKey1" => ["B-5"]
        ]);

        $bestAvailableObjects = $this->seatsioClient->events->changeBestAvailableObjectStatus($event->key, 1, "lolzor", null, null, null, null, null, null, true);

        self::assertEquals(["B-5"], $bestAvailableObjects->objects);
    }
}
