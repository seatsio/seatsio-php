<?php

namespace Seatsio\Events;

use Seatsio\SeatsioClientTest;

class ChangeObjectStatusForMultipleEventsTest extends SeatsioClientTest
{

    public function test()
    {
        $chartKey = $this->createTestChart();
        $event1 = $this->seatsioClient->events->create($chartKey);
        $event2 = $this->seatsioClient->events->create($chartKey);

        $this->seatsioClient->events->changeObjectStatus([$event1->key, $event2->key], "A-1", "lolzor");

        $objectInfo1 = $this->seatsioClient->events->retrieveObjectInfo($event1->key, "A-1");
        self::assertEquals("lolzor", $objectInfo1->status);

        $objectInfo2 = $this->seatsioClient->events->retrieveObjectInfo($event2->key, "A-1");
        self::assertEquals("lolzor", $objectInfo2->status);
    }

    public function testBook()
    {
        $chartKey = $this->createTestChart();
        $event1 = $this->seatsioClient->events->create($chartKey);
        $event2 = $this->seatsioClient->events->create($chartKey);

        $this->seatsioClient->events->book([$event1->key, $event2->key], "A-1");

        $objectInfo1 = $this->seatsioClient->events->retrieveObjectInfo($event1->key, "A-1");
        self::assertEquals(ObjectInfo::$BOOKED, $objectInfo1->status);

        $objectInfo2 = $this->seatsioClient->events->retrieveObjectInfo($event2->key, "A-1");
        self::assertEquals(ObjectInfo::$BOOKED, $objectInfo2->status);
    }

    public function testRelease()
    {
        $chartKey = $this->createTestChart();
        $event1 = $this->seatsioClient->events->create($chartKey);
        $event2 = $this->seatsioClient->events->create($chartKey);
        $this->seatsioClient->events->book([$event1->key, $event2->key], "A-1");

        $this->seatsioClient->events->release([$event1->key, $event2->key], "A-1");

        $objectInfo1 = $this->seatsioClient->events->retrieveObjectInfo($event1->key, "A-1");
        self::assertEquals(ObjectInfo::$FREE, $objectInfo1->status);

        $objectInfo2 = $this->seatsioClient->events->retrieveObjectInfo($event2->key, "A-1");
        self::assertEquals(ObjectInfo::$FREE, $objectInfo2->status);
    }

    public function testHold()
    {
        $chartKey = $this->createTestChart();
        $event1 = $this->seatsioClient->events->create($chartKey);
        $event2 = $this->seatsioClient->events->create($chartKey);
        $holdToken = $this->seatsioClient->holdTokens->create();

        $this->seatsioClient->events->hold([$event1->key, $event2->key], "A-1", $holdToken->holdToken);

        $objectInfo1 = $this->seatsioClient->events->retrieveObjectInfo($event1->key, "A-1");
        self::assertEquals(ObjectInfo::$HELD, $objectInfo1->status);

        $objectInfo2 = $this->seatsioClient->events->retrieveObjectInfo($event2->key, "A-1");
        self::assertEquals(ObjectInfo::$HELD, $objectInfo2->status);
    }

}
