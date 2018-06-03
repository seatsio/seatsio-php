<?php

namespace Seatsio\Events;

use Seatsio\SeatsioClientTest;

class MarkObjectsAsForSaleTest extends SeatsioClientTest
{

    public function test()
    {
        $chart = $this->seatsioClient->charts->create();
        $event = $this->seatsioClient->events->create($chart->key);

        $this->seatsioClient->events->markAsForSale($event->key, ["o1", "o2"], ["cat1", "cat2"]);

        $retrievedEvent = $this->seatsioClient->events->retrieve($event->key);
        self::assertTrue($retrievedEvent->forSaleConfig->forSale);
        self::assertEquals(["o1", "o2"], $retrievedEvent->forSaleConfig->objects);
        self::assertEquals(["cat1", "cat2"], $retrievedEvent->forSaleConfig->categories);
    }

    public function testCategoriesAreOptional()
    {
        $chart = $this->seatsioClient->charts->create();
        $event = $this->seatsioClient->events->create($chart->key);

        $this->seatsioClient->events->markAsForSale($event->key, ["o1", "o2"]);

        $retrievedEvent = $this->seatsioClient->events->retrieve($event->key);
        self::assertEquals(["o1", "o2"], $retrievedEvent->forSaleConfig->objects);
        self::assertEmpty($retrievedEvent->forSaleConfig->categories);
    }

    public function testObjectsAreOptional()
    {
        $chart = $this->seatsioClient->charts->create();
        $event = $this->seatsioClient->events->create($chart->key);

        $this->seatsioClient->events->markAsForSale($event->key, null, ["cat1", "cat2"]);

        $retrievedEvent = $this->seatsioClient->events->retrieve($event->key);
        self::assertEmpty($retrievedEvent->forSaleConfig->objects);
        self::assertEquals(["cat1", "cat2"], $retrievedEvent->forSaleConfig->categories);
    }

}