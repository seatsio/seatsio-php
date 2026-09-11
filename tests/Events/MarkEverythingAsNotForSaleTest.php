<?php

namespace Seatsio\Events;

use Seatsio\SeatsioClientTest;

class MarkEverythingAsNotForSaleTest extends SeatsioClientTest
{

    public function test()
    {
        $chart = $this->seatsioClient->charts->create();
        $event = $this->seatsioClient->events->create($chart->key);

        $this->seatsioClient->events->markEverythingAsNotForSale($event->key);

        $retrievedEvent = $this->seatsioClient->events->retrieve($event->key);
        self::assertFalse($retrievedEvent->forSaleConfig->forSale);
        self::assertEmpty($retrievedEvent->forSaleConfig->objects);
        self::assertEmpty($retrievedEvent->forSaleConfig->categories);
    }
}


