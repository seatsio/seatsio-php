<?php

namespace Seatsio\Events;

use Seatsio\SeatsioClientTest;

class RetrieveEventTest extends SeatsioClientTest
{

    public function test()
    {
        $chart = $this->seatsioClient->charts->create();
        $event = $this->seatsioClient->events->create($chart->key);

        $retrievedEvent = $this->seatsioClient->events->retrieve($event->key);

        self::assertEquals($event->key, $retrievedEvent->key);
        self::assertEquals($event->id, $retrievedEvent->id);
        self::assertEquals($chart->key, $retrievedEvent->chartKey);
        self::assertFalse($retrievedEvent->bookWholeTables);
        self::assertEquals($event->createdOn, $retrievedEvent->createdOn);
        self::assertNull($retrievedEvent->forSaleConfig);
        self::assertNull($retrievedEvent->updatedOn);
    }

}