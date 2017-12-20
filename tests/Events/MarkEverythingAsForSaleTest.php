<?php

namespace Seatsio\Events;

use Seatsio\SeatsioClientTest;

class MarkEverythingAsForSaleTest extends SeatsioClientTest
{

    public function test()
    {
        $chart = $this->seatsioClient->charts()->create();
        $event = $this->seatsioClient->events()->create($chart->key);
        $this->seatsioClient->events()->markAsForSale($event->key, ["o1", "o2"], ["cat1", "cat2"]);

        $this->seatsioClient->events()->markEverythingAsForSale($event->key);

        $retrievedEvent = $this->seatsioClient->events()->retrieve($event->key);
        self::assertNull($retrievedEvent->forSaleConfig);
    }

}