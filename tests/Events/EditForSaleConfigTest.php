<?php

namespace Events;

use Seatsio\Events\CreateEventParams;
use Seatsio\Events\ForSaleConfig;
use Seatsio\Events\ObjectAndQuantity;
use Seatsio\SeatsioClientTest;

class EditForSaleConfigTest extends SeatsioClientTest
{

    public function testMarkObjectsAsForSale()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey, (new CreateEventParams())->setForSaleConfig(new ForSaleConfig(false, ['A-1', 'A-2', 'A-3'])));

        $result = $this->seatsioClient->events->editForSaleConfig($event->key, [new ObjectAndQuantity("A-1"), new ObjectAndQuantity("A-2")]);

        self::assertFalse($result->forSaleConfig->forSale);
        self::assertEquals(["A-3"], $result->forSaleConfig->objects);
    }

    public function testReturnsRateLimiInfo()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey, (new CreateEventParams())->setForSaleConfig(new ForSaleConfig(false, ['A-1', 'A-2', 'A-3'])));

        $result = $this->seatsioClient->events->editForSaleConfig($event->key, [new ObjectAndQuantity("A-1"), new ObjectAndQuantity("A-2")]);

        self::assertEquals(9, $result->rateLimitInfo->rateLimitRemainingCalls);
        self::assertNotNull($result->rateLimitInfo->rateLimitResetDate);
    }

    public function testMarkObjectsAsNotForSale()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey);

        $result = $this->seatsioClient->events->editForSaleConfig($event->key, null, [new ObjectAndQuantity("A-1"), new ObjectAndQuantity("A-2")]);

        self::assertFalse($result->forSaleConfig->forSale);
        self::assertEquals(["A-1", "A-2"], $result->forSaleConfig->objects);
    }

    public function testMarkPlacesInAreaAsNotForSale()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey);

        $result = $this->seatsioClient->events->editForSaleConfig($event->key, null, [new ObjectAndQuantity("GA1", 5)]);

        self::assertEquals(["GA1" => 5], $result->forSaleConfig->areaPlaces);
    }
}
