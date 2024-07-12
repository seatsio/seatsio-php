<?php

namespace Seatsio\Events;

use Seatsio\SeatsioClientTest;

class MarkObjectsAsNotForSaleTest extends SeatsioClientTest
{

    public function test()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey);

        $this->seatsioClient->events->markAsNotForSale($event->key, ["o1", "o2"], ["GA1" => 3], ["cat1", "cat2"]);

        $retrievedEvent = $this->seatsioClient->events->retrieve($event->key);
        self::assertFalse($retrievedEvent->forSaleConfig->forSale);
        self::assertEquals(["o1", "o2"], $retrievedEvent->forSaleConfig->objects);
        self::assertEquals(["GA1" => 3], $retrievedEvent->forSaleConfig->areaPlaces);
        self::assertEquals(["cat1", "cat2"], $retrievedEvent->forSaleConfig->categories);
    }

    public function testCategoriesAndAreaPlacesAreOptional()
    {
        $chart = $this->seatsioClient->charts->create();
        $event = $this->seatsioClient->events->create($chart->key);

        $this->seatsioClient->events->markAsNotForSale($event->key, ["o1", "o2"]);

        $retrievedEvent = $this->seatsioClient->events->retrieve($event->key);
        self::assertEquals(["o1", "o2"], $retrievedEvent->forSaleConfig->objects);
        self::assertEmpty($retrievedEvent->forSaleConfig->areaPlaces);
        self::assertEmpty($retrievedEvent->forSaleConfig->categories);
    }

    public function testObjectsAreOptional()
    {
        $chart = $this->seatsioClient->charts->create();
        $event = $this->seatsioClient->events->create($chart->key);

        $this->seatsioClient->events->markAsNotForSale($event->key, null, null, ["cat1", "cat2"]);

        $retrievedEvent = $this->seatsioClient->events->retrieve($event->key);
        self::assertEmpty($retrievedEvent->forSaleConfig->objects);
        self::assertEquals(["cat1", "cat2"], $retrievedEvent->forSaleConfig->categories);
    }

    public function testNumNotForSaleIsCorrectlyExposed()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey);

        $this->seatsioClient->events->markAsNotForSale($event->key, [], ["GA1" => 3]);

        $eventObjectInfo = $this->seatsioClient->events->retrieveObjectInfo($event->key, "GA1");
        self::assertEquals(3, $eventObjectInfo->numNotForSale);
    }
}
