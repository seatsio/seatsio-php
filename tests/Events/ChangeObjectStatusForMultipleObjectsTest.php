<?php

namespace Seatsio\Events;

use Seatsio\SeatsioClientTest;

class ChangeObjectStatusForMultipleObjectsTest extends SeatsioClientTest
{

    public function testArrayOfStrings()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey);

        $this->seatsioClient->events->changeObjectStatus($event->key, ["A-1", "A-2"], "lolzor");

        self::assertEquals("lolzor", $this->seatsioClient->events->retrieveObjectInfo($event->key, "A-1")->status);
        self::assertEquals("lolzor", $this->seatsioClient->events->retrieveObjectInfo($event->key, "A-2")->status);
    }

    public function testArrayOfObjects()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey);
        $objects = [new ObjectProperties("A-1"), new ObjectProperties("A-2")];

        $this->seatsioClient->events->changeObjectStatus($event->key, $objects, "lolzor");

        self::assertEquals("lolzor", $this->seatsioClient->events->retrieveObjectInfo($event->key, "A-1")->status);
        self::assertEquals("lolzor", $this->seatsioClient->events->retrieveObjectInfo($event->key, "A-2")->status);
    }

    public function testArrayOfAssociativeArrays()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey);
        $objects = [["objectId" => "A-1"], ["objectId" => "A-2"]];

        $this->seatsioClient->events->changeObjectStatus($event->key, $objects, "lolzor");

        self::assertEquals("lolzor", $this->seatsioClient->events->retrieveObjectInfo($event->key, "A-1")->status);
        self::assertEquals("lolzor", $this->seatsioClient->events->retrieveObjectInfo($event->key, "A-2")->status);
    }

    public function testArrayOfAssociativeArraysWithGeneralAdmissionAreas()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey);
        $objects = [["objectId" => "A-1"], ["objectId" => "GA1", "quantity" => 5]];

        $this->seatsioClient->events->changeObjectStatus($event->key, $objects, "lolzor");

        self::assertEquals("lolzor", $this->seatsioClient->events->retrieveObjectInfo($event->key, "A-1")->status);

        self::assertEquals(5, $this->seatsioClient->events->retrieveObjectInfo($event->key, "GA1")->quantity);
    }

    public function testCombinationOfStringsAndAssociativeArrays()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey);
        $objects = ["A-1", ["objectId" => "GA1", "quantity" => 5]];

        $this->seatsioClient->events->changeObjectStatus($event->key, $objects, "lolzor");

        self::assertEquals("lolzor", $this->seatsioClient->events->retrieveObjectInfo($event->key, "A-1")->status);

        self::assertEquals(5, $this->seatsioClient->events->retrieveObjectInfo($event->key, "GA1")->quantity);
    }

    public function testTicketType()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey);
        $objects = [
            (new ObjectProperties("A-1"))->setTicketType("T1"),
            (new ObjectProperties("A-2"))->setTicketType("T2")
        ];

        $this->seatsioClient->events->changeObjectStatus($event->key, $objects, "lolzor");

        $objectInfo1 = $this->seatsioClient->events->retrieveObjectInfo($event->key, "A-1");
        self::assertEquals("lolzor", $objectInfo1->status);
        self::assertEquals("T1", $objectInfo1->ticketType);

        $objectInfo2 = $this->seatsioClient->events->retrieveObjectInfo($event->key, "A-2");
        self::assertEquals("lolzor", $objectInfo2->status);
        self::assertEquals("T2", $objectInfo2->ticketType);
    }

    public function testQuantity()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey);
        $objects = [
            (new ObjectProperties("GA1"))->setQuantity(5),
            (new ObjectProperties("34"))->setQuantity(10)
        ];

        $this->seatsioClient->events->changeObjectStatus($event->key, $objects, "lolzor");

        $objectInfo1 = $this->seatsioClient->events->retrieveObjectInfo($event->key, "GA1");
        self::assertEquals(5, $objectInfo1->quantity);

        $objectInfo2 = $this->seatsioClient->events->retrieveObjectInfo($event->key, "34");
        self::assertEquals(10, $objectInfo2->quantity);
    }

    public function testCombinationOfGAAndSeats()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey);
        $objects = [
            (new ObjectProperties("GA1"))->setQuantity(5),
            new ObjectProperties("A-1")
        ];

        $this->seatsioClient->events->changeObjectStatus($event->key, $objects, "lolzor");

        $objectInfo1 = $this->seatsioClient->events->retrieveObjectInfo($event->key, "GA1");
        self::assertEquals(5, $objectInfo1->quantity);

        $objectInfo2 = $this->seatsioClient->events->retrieveObjectInfo($event->key, "A-1");
        self::assertEquals("lolzor", $objectInfo2->status);
    }

    public function testExtraData()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey);
        $objects = [
            (new ObjectProperties("A-1"))->setExtraData(["foo" => "bar"]),
            (new ObjectProperties("A-2"))->setExtraData(["foo" => "baz"])
        ];

        $this->seatsioClient->events->changeObjectStatus($event->key, $objects, "lolzor");

        $objectInfo1 = $this->seatsioClient->events->retrieveObjectInfo($event->key, "A-1");
        self::assertEquals((object)(["foo" => "bar"]), $objectInfo1->extraData);

        $objectInfo2 = $this->seatsioClient->events->retrieveObjectInfo($event->key, "A-2");
        self::assertEquals((object)(["foo" => "baz"]), $objectInfo2->extraData);
    }

}
