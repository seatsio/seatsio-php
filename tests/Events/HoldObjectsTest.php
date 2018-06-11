<?php

namespace Seatsio\Events;

use Seatsio\SeatsioClientTest;

class HoldObjectsTest extends SeatsioClientTest
{

    public function test()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey);
        $holdToken = $this->seatsioClient->holdTokens->create();

        $res = $this->seatsioClient->events->hold($event->key, ["A-1", "A-2"], $holdToken->holdToken);

        $status1 = $this->seatsioClient->events->retrieveObjectStatus($event->key, "A-1");
        self::assertEquals(ObjectStatus::$HELD, $status1->status);
        self::assertEquals($holdToken->holdToken, $status1->holdToken);

        $status2 = $this->seatsioClient->events->retrieveObjectStatus($event->key, "A-2");
        self::assertEquals(ObjectStatus::$HELD, $status2->status);
        self::assertEquals($holdToken->holdToken, $status2->holdToken);

        self::assertEquals([
            "A-1" => someLabels("1", "seat", "A", "row"),
            "A-2" => someLabels("2", "seat", "A", "row")
        ], $res->labels);
    }

    public function testOrderId()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey);
        $holdToken = $this->seatsioClient->holdTokens->create();

        $this->seatsioClient->events->hold($event->key, "A-1", $holdToken->holdToken, "order1");

        $objectStatus = $this->seatsioClient->events->retrieveObjectStatus($event->key, "A-1");
        self::assertEquals("order1", $objectStatus->orderId);
    }

}