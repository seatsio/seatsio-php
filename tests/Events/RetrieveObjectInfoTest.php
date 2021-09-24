<?php

namespace Seatsio\Events;

use Seatsio\SeatsioClientTest;

class RetrieveObjectInfoTest extends SeatsioClientTest
{

    public function test()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey);

        $objectInfo = $this->seatsioClient->events->retrieveObjectInfo($event->key, "A-1");
        self::assertEquals(ObjectInfo::$FREE, $objectInfo->status);
        self::assertNull($objectInfo->ticketType);
        self::assertNull($objectInfo->extraData);
        self::assertTrue($objectInfo->forSale);
    }

}
