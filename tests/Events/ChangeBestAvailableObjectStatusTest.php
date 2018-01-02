<?php

namespace Seatsio\Events;

use Seatsio\SeatsioClientTest;

class ChangeBestAvailableObjectStatusTest extends SeatsioClientTest
{

    public function test()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events()->create($chartKey);

        $bestAvailableObjects = $this->seatsioClient->events()->changeBestAvailableObjectStatus($event->key, 3, "lolzor");

        self::assertTrue($bestAvailableObjects->nextToEachOther);
        self::assertEquals(["B-3", "B-4", "B-5"], $bestAvailableObjects->objects, '', 0.0, 10, true);
    }

    public function testCategories()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events()->create($chartKey);

        $bestAvailableObjects = $this->seatsioClient->events()->changeBestAvailableObjectStatus($event->key, 3, "lolzor", ["cat2"]);

        self::assertEquals(["C-3", "C-4", "C-5"], $bestAvailableObjects->objects, '', 0.0, 10, true);
    }

    public function testUseObjectUuidsInsteadOfLabels()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events()->create($chartKey);

        $bestAvailableObjects = $this->seatsioClient->events()->changeBestAvailableObjectStatus($event->key, 3, "lolzor", null, true);

        self::assertEquals(["uuid300", "uuid301", "uuid302"], $bestAvailableObjects->objects, '', 0.0, 10, true);
    }

    public function testHoldToken()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events()->create($chartKey);
        $holdToken = $this->seatsioClient->holdTokens()->create();

        $bestAvailableObjects = $this->seatsioClient->events()->changeBestAvailableObjectStatus($event->key, 1, ObjectStatus::$HELD, null, null, $holdToken->holdToken);

        $objectStatus = $this->seatsioClient->events()->getObjectStatus($event->key, $bestAvailableObjects->objects[0]);
        self::assertEquals(ObjectStatus::$HELD, $objectStatus->status);
        self::assertEquals($holdToken->holdToken, $objectStatus->holdToken);
    }

    public function testOrderId()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events()->create($chartKey);

        $bestAvailableObjects = $this->seatsioClient->events()->changeBestAvailableObjectStatus($event->key, 1, "lolzor", null, null, null, "anOrder");

        $objectStatus = $this->seatsioClient->events()->getObjectStatus($event->key, $bestAvailableObjects->objects[0]);
        self::assertEquals("anOrder", $objectStatus->orderId);
    }

    public function book()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events()->create($chartKey);

        $bestAvailableObjects = $this->seatsioClient->events()->bookBestAvailable($event->key, 3);

        self::assertTrue($bestAvailableObjects->nextToEachOther);
        self::assertEquals(["B-3", "B-4", "B-5"], $bestAvailableObjects->objects, '', 0.0, 10, true);
    }

    public function hold()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events()->create($chartKey);
        $holdToken = $this->seatsioClient->holdTokens()->create();

        $bestAvailableObjects = $this->seatsioClient->events()->holdBestAvailable($event->key, 1, $holdToken->holdToken);

        $objectStatus = $this->seatsioClient->events()->getObjectStatus($event->key, $bestAvailableObjects->objects[0]);
        self::assertEquals(ObjectStatus::$HELD, $objectStatus->status);
        self::assertEquals($holdToken->holdToken, $objectStatus->holdToken);
    }
}