<?php

namespace Seatsio\Events;

use Seatsio\SeatsioClientTest;

class ChangeObjectStatusTest extends SeatsioClientTest
{

    public function testObjectIdAsString()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events()->create($chartKey);

        $this->seatsioClient->events()->changeObjectStatus($event->key, "A-1", "lolzor");

        $objectStatus = $this->seatsioClient->events()->getObjectStatus($event->key, "A-1");
        self::assertEquals("lolzor", $objectStatus->status);
    }

    public function testObjectIdInsideObject()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events()->create($chartKey);

        $this->seatsioClient->events()->changeObjectStatus($event->key, new Object("A-1"), "lolzor");

        $objectStatus = $this->seatsioClient->events()->getObjectStatus($event->key, "A-1");
        self::assertEquals("lolzor", $objectStatus->status);
    }

    public function testHoldToken()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events()->create($chartKey);
        $holdToken = $this->seatsioClient->holdTokens()->create();

        $this->seatsioClient->events()->changeObjectStatus($event->key, "A-1", "reservedByToken", $holdToken->holdToken);

        $objectStatus = $this->seatsioClient->events()->getObjectStatus($event->key, "A-1");
        self::assertEquals("reservedByToken", $objectStatus->status);
        self::assertEquals($holdToken->holdToken, $objectStatus->holdToken);
    }

    public function testOrderId()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events()->create($chartKey);

        $this->seatsioClient->events()->changeObjectStatus($event->key, "A-1", "lolzor", null, "order1");

        $objectStatus = $this->seatsioClient->events()->getObjectStatus($event->key, "A-1");
        self::assertEquals("order1", $objectStatus->orderId);
    }

}