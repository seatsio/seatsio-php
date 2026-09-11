<?php

namespace Seatsio\Events;

use Seatsio\SeatsioClientTest;

class MarkEverythingAsForNotSaleTest extends SeatsioClientTest
{

    public function test()
    {
        $chart = $this->seatsioClient->charts->create();
        $event = $this->seatsioClient->events->create($chart->key);

        $this->seatsioClient->events->markEverythingAsForNotSale($event->key);

        $retrievedEvent = $this->seatsioClient->events->retrieve($event->key);
        self::assertFalse($retrievedEvent->forSaleConfig->forSale);
        self::assertEmpty($retrievedEvent->forSaleConfig->objects);
        self::assertEmpty($retrievedEvent->forSaleConfig->categories);
    }
}

