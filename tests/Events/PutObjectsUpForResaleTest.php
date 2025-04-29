<?php

namespace Seatsio\Charts;

use Seatsio\Events\EventObjectInfo;
use Seatsio\SeatsioClientTest;

class PutObjectsUpForResaleTest extends SeatsioClientTest
{

    public function test()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey);

        $res = $this->seatsioClient->events->putUpForResale($event->key, ["A-1", "A-2"], "listing1");

        self::assertEquals(EventObjectInfo::$RESALE, $this->seatsioClient->events->retrieveObjectInfo($event->key, "A-1")->status);
        self::assertEquals(EventObjectInfo::$RESALE, $this->seatsioClient->events->retrieveObjectInfo($event->key, "A-2")->status);

        self::assertEquals(["A-1", "A-2"], SeatsioClientTest::sort(array_keys($res->objects)));
        self::assertEquals($res->objects["A-1"]->resaleListingId, "listing1");
        self::assertEquals($res->objects["A-2"]->resaleListingId, "listing1");
    }

    public function testMultipleEvents()
    {
        $chartKey = $this->createTestChart();
        $event1 = $this->seatsioClient->events->create($chartKey);
        $event2 = $this->seatsioClient->events->create($chartKey);

        $res = $this->seatsioClient->events->putUpForResale([$event1->key, $event2->key], "A-1");

        $objectInfo1 = $this->seatsioClient->events->retrieveObjectInfo($event1->key, "A-1");
        self::assertEquals(EventObjectInfo::$RESALE, $objectInfo1->status);

        $objectInfo2 = $this->seatsioClient->events->retrieveObjectInfo($event2->key, "A-1");
        self::assertEquals(EventObjectInfo::$RESALE, $objectInfo2->status);
    }

}
