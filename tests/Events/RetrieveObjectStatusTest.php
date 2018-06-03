<?php

namespace Seatsio\Events;

use Seatsio\SeatsioClientTest;

class RetrieveObjectStatusTest extends SeatsioClientTest
{

    public function test()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey);

        $objectStatus = $this->seatsioClient->events->retrieveObjectStatus($event->key, "A-1");
        self::assertEquals(ObjectStatus::$FREE, $objectStatus->status);
        self::assertNull($objectStatus->ticketType);
        self::assertNull($objectStatus->extraData);
    }

}