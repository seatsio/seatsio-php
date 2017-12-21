<?php

namespace Seatsio\Events;

use Seatsio\SeatsioClientTest;

class BookObjectsTest extends SeatsioClientTest
{

    public function test()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events()->create($chartKey);

        $this->seatsioClient->events()->book($event->key, ["A-1", "A-2"]);

        self::assertEquals("booked", $this->seatsioClient->events()->getObjectStatus($event->key, "A-1")->status);
        self::assertEquals("booked", $this->seatsioClient->events()->getObjectStatus($event->key, "A-2")->status);
    }

    public function testHoldToken()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events()->create($chartKey);
        $holdToken = $this->seatsioClient->holdTokens()->create();
        $this->seatsioClient->events()->reserve($event->key, "A-1", $holdToken->holdToken);

        $this->seatsioClient->events()->book($event->key, "A-1", $holdToken->holdToken);

        $objectStatus = $this->seatsioClient->events()->getObjectStatus($event->key, "A-1");
        self::assertEquals("booked", $objectStatus->status);
        self::assertNull($objectStatus->holdToken);
    }

    public function testOrderId()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events()->create($chartKey);

        $this->seatsioClient->events()->book($event->key, "A-1", null, "order1");

        $objectStatus = $this->seatsioClient->events()->getObjectStatus($event->key, "A-1");
        self::assertEquals("order1", $objectStatus->orderId);
    }

}