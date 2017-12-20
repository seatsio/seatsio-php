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

}