<?php

namespace Seatsio\Events;

use Seatsio\SeatsioClientTest;

class ChangeObjectStatusForMultipleEventsTest extends SeatsioClientTest
{

    public function test()
    {
        $chartKey = $this->createTestChart();
        $event1 = $this->seatsioClient->events()->create($chartKey);
        $event2 = $this->seatsioClient->events()->create($chartKey);

        $this->seatsioClient->events()->changeObjectStatus([$event1->key, $event2->key], "A-1", "lolzor");

        $objectStatus1 = $this->seatsioClient->events()->getObjectStatus($event1->key, "A-1");
        self::assertEquals("lolzor", $objectStatus1->status);

        $objectStatus2 = $this->seatsioClient->events()->getObjectStatus($event2->key, "A-1");
        self::assertEquals("lolzor", $objectStatus2->status);
    }

    public function testBook()
    {
        $chartKey = $this->createTestChart();
        $event1 = $this->seatsioClient->events()->create($chartKey);
        $event2 = $this->seatsioClient->events()->create($chartKey);

        $this->seatsioClient->events()->book([$event1->key, $event2->key], "A-1");

        $objectStatus1 = $this->seatsioClient->events()->getObjectStatus($event1->key, "A-1");
        self::assertEquals("booked", $objectStatus1->status);

        $objectStatus2 = $this->seatsioClient->events()->getObjectStatus($event2->key, "A-1");
        self::assertEquals("booked", $objectStatus2->status);
    }

    public function testRelease()
    {
        $chartKey = $this->createTestChart();
        $event1 = $this->seatsioClient->events()->create($chartKey);
        $event2 = $this->seatsioClient->events()->create($chartKey);
        $this->seatsioClient->events()->book([$event1->key, $event2->key], "A-1");

        $this->seatsioClient->events()->release([$event1->key, $event2->key], "A-1");

        $objectStatus1 = $this->seatsioClient->events()->getObjectStatus($event1->key, "A-1");
        self::assertEquals("free", $objectStatus1->status);

        $objectStatus2 = $this->seatsioClient->events()->getObjectStatus($event2->key, "A-1");
        self::assertEquals("free", $objectStatus2->status);
    }

    public function testHold()
    {
        $chartKey = $this->createTestChart();
        $event1 = $this->seatsioClient->events()->create($chartKey);
        $event2 = $this->seatsioClient->events()->create($chartKey);
        $holdToken = $this->seatsioClient->holdTokens()->create();

        $this->seatsioClient->events()->hold([$event1->key, $event2->key], "A-1", $holdToken->holdToken);

        $objectStatus1 = $this->seatsioClient->events()->getObjectStatus($event1->key, "A-1");
        self::assertEquals("reservedByToken", $objectStatus1->status);

        $objectStatus2 = $this->seatsioClient->events()->getObjectStatus($event2->key, "A-1");
        self::assertEquals("reservedByToken", $objectStatus2->status);
    }

}