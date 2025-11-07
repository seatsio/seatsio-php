<?php

namespace Events;

use Seatsio\Events\CreateEventParams;
use Seatsio\Events\ForSaleConfig;
use Seatsio\Events\ForSaleConfigParams;
use Seatsio\Events\ObjectAndQuantity;
use Seatsio\SeatsioClientTest;

class EditForSaleConfigForEventsTest extends SeatsioClientTest
{

    public function testMarkObjectsAsForSale()
    {
        $chartKey = $this->createTestChart();
        $event1 = $this->seatsioClient->events->create($chartKey, (new CreateEventParams())->setForSaleConfig(new ForSaleConfig(false, ['A-1', 'A-2', 'A-3'])));
        $event2 = $this->seatsioClient->events->create($chartKey, (new CreateEventParams())->setForSaleConfig(new ForSaleConfig(false, ['A-1', 'A-2', 'A-3'])));

        $forSaleConfigs = $this->seatsioClient->events->editForSaleConfigForEvents([
            $event1->key => new ForSaleConfigParams([new ObjectAndQuantity("A-1")]),
            $event2->key => new ForSaleConfigParams([new ObjectAndQuantity("A-2")])
        ]);

        self::assertFalse($forSaleConfigs[$event1->key]->forSale);
        self::assertEquals(["A-2", "A-3"], $forSaleConfigs[$event1->key]->objects);

        self::assertFalse($forSaleConfigs[$event2->key]->forSale);
        self::assertEquals(["A-1", "A-3"], $forSaleConfigs[$event2->key]->objects);
    }
}
