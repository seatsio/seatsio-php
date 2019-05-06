<?php

namespace Seatsio\Events;

use Seatsio\SeatsioClientTest;

class ReleaseObjectsTest extends SeatsioClientTest
{

    public function test()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey);
        $this->seatsioClient->events->book($event->key, ["A-1", "A-2"]);

        $res = $this->seatsioClient->events->release($event->key, ["A-1", "A-2"]);

        self::assertEquals(ObjectStatus::$FREE, $this->seatsioClient->events->retrieveObjectStatus($event->key, "A-1")->status);
        self::assertEquals(ObjectStatus::$FREE, $this->seatsioClient->events->retrieveObjectStatus($event->key, "A-2")->status);

        self::assertEquals(["A-1", "A-2"], array_keys($res->objects));
    }

    public function testHoldToken()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey);
        $holdToken = $this->seatsioClient->holdTokens->create();
        $this->seatsioClient->events->hold($event->key, "A-1", $holdToken->holdToken);

        $this->seatsioClient->events->release($event->key, "A-1", $holdToken->holdToken);

        $objectStatus = $this->seatsioClient->events->retrieveObjectStatus($event->key, "A-1");
        self::assertNull($objectStatus->holdToken);
    }

    public function testOrderId()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey);
        $this->seatsioClient->events->book($event->key, "A-1");

        $this->seatsioClient->events->release($event->key, "A-1", null, "order1");

        $objectStatus = $this->seatsioClient->events->retrieveObjectStatus($event->key, "A-1");
        self::assertEquals("order1", $objectStatus->orderId);
    }

    public function testKeepExtraData()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey);
        $this->seatsioClient->events->book($event->key, "A-1");
        $extraData = ["foo" => "bar"];
        $this->seatsioClient->events->updateExtraData($event->key, "A-1", $extraData);

        $this->seatsioClient->events->release($event->key, "A-1", null, null, true);

        $objectStatus = $this->seatsioClient->events->retrieveObjectStatus($event->key, "A-1");
        self::assertEquals((object)$extraData, $objectStatus->extraData);
    }

}
