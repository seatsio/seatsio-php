<?php

namespace Seatsio\Events;

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
        $b4Status = $this->seatsioClient->events->retrieveObjectStatus($event->key, "B-4");
        self::assertEquals($b4Status->extraData, (object)["foo" => "bar"]);
        $b5Status = $this->seatsioClient->events->retrieveObjectStatus($event->key, "B-5");
        self::assertEquals($b5Status->extraData, (object)["foo" => "baz"]);
    }

    public function testHoldToken()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey);
        $holdToken = $this->seatsioClient->holdTokens->create();

        $bestAvailableObjects = $this->seatsioClient->events->changeBestAvailableObjectStatus($event->key, 1, ObjectStatus::$HELD, null, $holdToken->holdToken);

        $objectStatus = $this->seatsioClient->events->retrieveObjectStatus($event->key, $bestAvailableObjects->objects[0]);
        self::assertEquals(ObjectStatus::$HELD, $objectStatus->status);
        self::assertEquals($holdToken->holdToken, $objectStatus->holdToken);
    }

    public function testOrderId()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey);

        $bestAvailableObjects = $this->seatsioClient->events->changeBestAvailableObjectStatus($event->key, 1, "lolzor", null, null, null, "anOrder");

        $objectStatus = $this->seatsioClient->events->retrieveObjectStatus($event->key, $bestAvailableObjects->objects[0]);
        self::assertEquals("anOrder", $objectStatus->orderId);
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

        $objectStatus = $this->seatsioClient->events->retrieveObjectStatus($event->key, $bestAvailableObjects->objects[0]);
        self::assertEquals(ObjectStatus::$HELD, $objectStatus->status);
        self::assertEquals($holdToken->holdToken, $objectStatus->holdToken);
    }

    public function testKeepExtraData()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey);
        $extraData = ["foo" => "bar"];
        $this->seatsioClient->events->updateExtraData($event->key, "B-5", $extraData);

        $this->seatsioClient->events->changeBestAvailableObjectStatus($event->key, 1, "lolzor", null, null, null, null, true);

        $objectStatus = $this->seatsioClient->events->retrieveObjectStatus($event->key, "B-5");
        self::assertEquals((object)$extraData, $objectStatus->extraData);
    }
}
