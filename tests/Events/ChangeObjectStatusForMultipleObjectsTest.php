<?php

namespace Seatsio\Events;

use Seatsio\SeatsioClientTest;

class ChangeObjectStatusForMultipleObjectsTest extends SeatsioClientTest
{

    public function testArrayOfStrings()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events()->create($chartKey);

        $this->seatsioClient->events()->changeObjectStatus($event->key, ["A-1", "A-2"], "lolzor");

        self::assertEquals("lolzor", $this->seatsioClient->events()->getObjectStatus($event->key, "A-1")->status);
        self::assertEquals("lolzor", $this->seatsioClient->events()->getObjectStatus($event->key, "A-2")->status);
    }

    public function testArrayOfObjects()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events()->create($chartKey);
        $objects = [new SeatsioObject("A-1"), new SeatsioObject("A-2")];

        $this->seatsioClient->events()->changeObjectStatus($event->key, $objects, "lolzor");

        self::assertEquals("lolzor", $this->seatsioClient->events()->getObjectStatus($event->key, "A-1")->status);
        self::assertEquals("lolzor", $this->seatsioClient->events()->getObjectStatus($event->key, "A-2")->status);
    }

    public function testArrayOfAssociativeArrays()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events()->create($chartKey);
        $objects = [["objectId" => "A-1"], ["objectId" => "A-2"]];

        $this->seatsioClient->events()->changeObjectStatus($event->key, $objects, "lolzor");

        self::assertEquals("lolzor", $this->seatsioClient->events()->getObjectStatus($event->key, "A-1")->status);
        self::assertEquals("lolzor", $this->seatsioClient->events()->getObjectStatus($event->key, "A-2")->status);
    }

    public function testTicketType()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events()->create($chartKey);
        $objects = [
            (new SeatsioObject("A-1"))->withTicketType("T1"),
            (new SeatsioObject("A-2"))->withTicketType("T2")
        ];

        $this->seatsioClient->events()->changeObjectStatus($event->key, $objects, "lolzor");

        $status1 = $this->seatsioClient->events()->getObjectStatus($event->key, "A-1");
        self::assertEquals("lolzor", $status1->status);
        self::assertEquals("T1", $status1->ticketType);

        $status2 = $this->seatsioClient->events()->getObjectStatus($event->key, "A-2");
        self::assertEquals("lolzor", $status2->status);
        self::assertEquals("T2", $status2->ticketType);
    }

    public function testQuantity()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events()->create($chartKey);
        $objects = [
            (new SeatsioObject("GA1"))->withQuantity(5),
            (new SeatsioObject("GA2"))->withQuantity(10)
        ];

        $this->seatsioClient->events()->changeObjectStatus($event->key, $objects, "lolzor");

        $status1 = $this->seatsioClient->events()->getObjectStatus($event->key, "GA1");
        self::assertEquals(5, $status1->quantity);

        $status2 = $this->seatsioClient->events()->getObjectStatus($event->key, "GA2");
        self::assertEquals(10, $status2->quantity);
    }

    public function testExtraData()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events()->create($chartKey);
        $objects = [
            (new SeatsioObject("A-1"))->withExtraData(["foo" => "bar"]),
            (new SeatsioObject("A-2"))->withExtraData(["foo" => "baz"])
        ];

        $this->seatsioClient->events()->changeObjectStatus($event->key, $objects, "lolzor");

        $status1 = $this->seatsioClient->events()->getObjectStatus($event->key, "A-1");
        self::assertEquals((object)(["foo" => "bar"]), $status1->extraData);

        $status2 = $this->seatsioClient->events()->getObjectStatus($event->key, "A-2");
        self::assertEquals((object)(["foo" => "baz"]), $status2->extraData);
    }

}