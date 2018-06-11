<?php

namespace Seatsio\Events;

use Seatsio\SeatsioClientTest;

class ChangeObjectStatusTest extends SeatsioClientTest
{

    public function test()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey);

        $result = $this->seatsioClient->events->changeObjectStatus($event->key, "A-1", "lolzor");

        self::assertEquals([
            "A-1" => someLabels("1", "seat", "A", "row")
        ], $result->labels);
    }

    public function testObjectIdAsString()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey);

        $this->seatsioClient->events->changeObjectStatus($event->key, "A-1", "lolzor");

        $objectStatus = $this->seatsioClient->events->retrieveObjectStatus($event->key, "A-1");
        self::assertEquals("lolzor", $objectStatus->status);
    }

    public function testObjectIdInsideObject()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey);

        $this->seatsioClient->events->changeObjectStatus($event->key, new ObjectProperties("A-1"), "lolzor");

        $objectStatus = $this->seatsioClient->events->retrieveObjectStatus($event->key, "A-1");
        self::assertEquals("lolzor", $objectStatus->status);
    }

    public function testHoldToken()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey);
        $holdToken = $this->seatsioClient->holdTokens->create();

        $this->seatsioClient->events->changeObjectStatus($event->key, "A-1", ObjectStatus::$HELD, $holdToken->holdToken);

        $objectStatus = $this->seatsioClient->events->retrieveObjectStatus($event->key, "A-1");
        self::assertEquals(ObjectStatus::$HELD, $objectStatus->status);
        self::assertEquals($holdToken->holdToken, $objectStatus->holdToken);
    }

    public function testOrderId()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey);

        $this->seatsioClient->events->changeObjectStatus($event->key, "A-1", "lolzor", null, "order1");

        $objectStatus = $this->seatsioClient->events->retrieveObjectStatus($event->key, "A-1");
        self::assertEquals("order1", $objectStatus->orderId);
    }

}