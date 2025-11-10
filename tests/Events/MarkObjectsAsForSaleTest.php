<?php

namespace Seatsio\Events;

use Seatsio\SeatsioClientTest;

class MarkObjectsAsForSaleTest extends SeatsioClientTest
{

    public function test()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey);

        $this->seatsioClient->events->markAsForSale($event->key, ["o1", "o2"], ["GA1" => 3], ["cat1", "cat2"]);

        $retrievedEvent = $this->seatsioClient->events->retrieve($event->key);
        self::assertTrue($retrievedEvent->forSaleConfig->forSale);
        self::assertEquals(["o1", "o2"], $retrievedEvent->forSaleConfig->objects);
        self::assertEquals(["GA1" => 3], $retrievedEvent->forSaleConfig->areaPlaces);
        self::assertEquals(["cat1", "cat2"], $retrievedEvent->forSaleConfig->categories);
    }
}
