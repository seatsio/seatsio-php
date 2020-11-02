<?php

namespace Seatsio\Events;

use Seatsio\SeatsioClientTest;

class RetrieveEventTest extends SeatsioClientTest
{

    public function test()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey);

        $retrievedEvent = $this->seatsioClient->events->retrieve($event->key);

        self::assertEquals($event->key, $retrievedEvent->key);
        self::assertEquals($event->id, $retrievedEvent->id);
        self::assertEquals($chartKey, $retrievedEvent->chartKey);
        self::assertEquals(TableBookingConfig::inherit(), $retrievedEvent->tableBookingConfig);
        self::assertTrue($retrievedEvent->supportsBestAvailable);
        self::assertEquals($event->createdOn, $retrievedEvent->createdOn);
        self::assertNull($retrievedEvent->forSaleConfig);
        self::assertNull($retrievedEvent->updatedOn);
    }

}
