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

    public function testTableSeat()
    {
        $chartKey = $this->createTestChartWithTables();
        $event = $this->seatsioClient->events->create($chartKey);

        $result = $this->seatsioClient->events->changeObjectStatus($event->key, "T1-1", "lolzor");

        self::assertEquals([
            "T1-1" => someLabels("1", "seat", "T1", "table")
        ], $result->labels);
    }

    public function testTable()
    {
        $chartKey = $this->createTestChartWithTables();
        $event = $this->seatsioClient->events->create($chartKey, null, true);

        $result = $this->seatsioClient->events->changeObjectStatus($event->key, "T1", "lolzor");

        self::assertEquals([
            "T1" => someLabels("T1", "table")
        ], $result->labels);
    }

    public function testGA()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey);

        $result = $this->seatsioClient->events->changeObjectStatus($event->key, "GA1", "lolzor");

        self::assertEquals([
            "GA1" => someLabels("GA1", "generalAdmission")
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