<?php

namespace Seatsio\Events;

use Seatsio\SeatsioClientTest;

class RetrieveEventObjectInfosTest extends SeatsioClientTest
{

    public function test()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey);

        $objectInfos = $this->seatsioClient->events->retrieveObjectInfos($event->key, ["A-1", "A-2"]);

        self::assertEquals(EventObjectInfo::$FREE, $objectInfos["A-1"]->status);
        self::assertEquals(EventObjectInfo::$FREE, $objectInfos["A-2"]->status);
        self::assertArrayNotHasKey("A-3", $objectInfos);
    }

    public function testHolds()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey);
        $holdToken = $this->seatsioClient->holdTokens->create();
        $this->seatsioClient->events->hold($event->key, 'GA1', $holdToken->holdToken);

        $objectInfos = $this->seatsioClient->events->retrieveObjectInfos($event->key, ["GA1"]);

        $expectedHolds = (object)[$holdToken->holdToken => (object)["NO_TICKET_TYPE" => 1]];
        self::assertEquals($expectedHolds, $objectInfos["GA1"]->holds);
    }

}
