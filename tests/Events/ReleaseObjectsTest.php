<?php

namespace Seatsio\Events;

use Seatsio\SeatsioClientTest;

class ReleaseObjectsTest extends SeatsioClientTest
{

    public function test()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey);
        $this->seatsioClient->events->book($event->key, ["A-1", "A-2"]);

        $res = $this->seatsioClient->events->release($event->key, ["A-1", "A-2"]);

        self::assertEquals(EventObjectInfo::$FREE, $this->seatsioClient->events->retrieveObjectInfo($event->key, "A-1")->status);
        self::assertEquals(EventObjectInfo::$FREE, $this->seatsioClient->events->retrieveObjectInfo($event->key, "A-2")->status);

        self::assertEquals(["A-1", "A-2"], SeatsioClientTest::sort(array_keys($res->objects)));
    }

    public function testHoldToken()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey);
        $holdToken = $this->seatsioClient->holdTokens->create();
        $this->seatsioClient->events->hold($event->key, "A-1", $holdToken->holdToken);

        $this->seatsioClient->events->release($event->key, "A-1", $holdToken->holdToken);

        $objectInfo = $this->seatsioClient->events->retrieveObjectInfo($event->key, "A-1");
        self::assertNull($objectInfo->holdToken);
    }

    public function testOrderId()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey);
        $this->seatsioClient->events->book($event->key, "A-1");

        $this->seatsioClient->events->release($event->key, "A-1", null, "order1");

        $objectInfo = $this->seatsioClient->events->retrieveObjectInfo($event->key, "A-1");
        self::assertEquals("order1", $objectInfo->orderId);
    }

    public function testKeepExtraData()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey);
        $this->seatsioClient->events->book($event->key, "A-1");
        $extraData = ["foo" => "bar"];
        $this->seatsioClient->events->updateExtraData($event->key, "A-1", $extraData);

        $this->seatsioClient->events->release($event->key, "A-1", null, null, true);

        $objectInfo = $this->seatsioClient->events->retrieveObjectInfo($event->key, "A-1");
        self::assertEquals((object)$extraData, $objectInfo->extraData);
    }

    public function testChannelKeys()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey);
        $this->seatsioClient->events->channels->replace($event->key, [
            new Channel("channelKey1", "channel 1", "#FF0000", 1, ["A-1", "A-2"])
        ]);
        $this->seatsioClient->events->book($event->key, "A-1", null, null, null, null, ["channelKey1"]);

        $this->seatsioClient->events->release($event->key, "A-1", null, null, null, null, ["channelKey1"]);

        $objectInfo = $this->seatsioClient->events->retrieveObjectInfo($event->key, "A-1");
        self::assertEquals(EventObjectInfo::$FREE, $objectInfo->status);
    }

    public function testIgnoreChannels()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey);
        $this->seatsioClient->events->channels->replace($event->key, [
            new Channel("channelKey1", "channel 1", "#FF0000", 1, ["A-1", "A-2"])
        ]);
        $this->seatsioClient->events->book($event->key, "A-1", null, null, null, null, ["channelKey1"]);

        $this->seatsioClient->events->release($event->key, "A-1", null, null, null, true);

        $objectInfo = $this->seatsioClient->events->retrieveObjectInfo($event->key, "A-1");
        self::assertEquals(EventObjectInfo::$FREE, $objectInfo->status);
    }
}
