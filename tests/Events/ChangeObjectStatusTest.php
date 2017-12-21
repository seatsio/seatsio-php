<?php

namespace Seatsio\Events;

use Seatsio\SeatsioClientTest;

class ChangeObjectStatusTest extends SeatsioClientTest
{

    public function test()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events()->create($chartKey);

        $this->seatsioClient->events()->changeObjectStatus($event->key, "A-1", "lolzor");

        $objectStatus = $this->seatsioClient->events()->getObjectStatus($event->key, "A-1");
        self::assertEquals("lolzor", $objectStatus->status);
    }

    public function testMultipleObjects()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events()->create($chartKey);

        $this->seatsioClient->events()->changeObjectStatus($event->key, ["A-1", "A-2"], "lolzor");

        self::assertEquals("lolzor", $this->seatsioClient->events()->getObjectStatus($event->key, "A-1")->status);
        self::assertEquals("lolzor", $this->seatsioClient->events()->getObjectStatus($event->key, "A-2")->status);
    }

    public function testChangeObjectStatusWithHoldToken()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events()->create($chartKey);
        $holdToken = $this->seatsioClient->holdTokens()->create();

        $this->seatsioClient->events()->changeObjectStatus($event->key, "A-1", "reservedByToken", $holdToken->holdToken);

        $objectStatus = $this->seatsioClient->events()->getObjectStatus($event->key, "A-1");
        self::assertEquals("reservedByToken", $objectStatus->status);
        self::assertEquals($holdToken->holdToken, $objectStatus->holdToken);
    }

    public function testChangeObjectStatusWithOrderId()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events()->create($chartKey);

        $this->seatsioClient->events()->changeObjectStatus($event->key, "A-1", "lolzor", null, "order1");

        $objectStatus = $this->seatsioClient->events()->getObjectStatus($event->key, "A-1");
        self::assertEquals("order1", $objectStatus->orderId);
    }

}