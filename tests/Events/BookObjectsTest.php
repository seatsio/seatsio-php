<?php

namespace Seatsio\Events;

use Seatsio\Common\IDs;
use Seatsio\SeatsioClientTest;

class BookObjectsTest extends SeatsioClientTest
{

    public function test()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey);

        $res = $this->seatsioClient->events->book($event->key, ["A-1", "A-2"]);

        self::assertEquals(EventObjectInfo::$BOOKED, $this->seatsioClient->events->retrieveObjectInfo($event->key, "A-1")->status);
        self::assertEquals(EventObjectInfo::$BOOKED, $this->seatsioClient->events->retrieveObjectInfo($event->key, "A-2")->status);

        self::assertEquals(["A-1", "A-2"], SeatsioClientTest::sort(array_keys($res->objects)));
    }

    public function testFloors()
    {
        $chartKey = $this->createTestChartWithFloors();
        $event = $this->seatsioClient->events->create($chartKey);

        $res = $this->seatsioClient->events->book($event->key, ["S1-A-1"]);

        $a1ObjectInfo = $res->objects["S1-A-1"];

        self::assertEquals(aFloor("1", "Floor 1"), $a1ObjectInfo->floor);
    }

    public function testSections()
    {
        $chartKey = $this->createTestChartWithSections();
        $event = $this->seatsioClient->events->create($chartKey);

        $res = $this->seatsioClient->events->book($event->key, ["Section A-A-1", "Section A-A-2"]);

        self::assertEquals(EventObjectInfo::$BOOKED, $this->seatsioClient->events->retrieveObjectInfo($event->key, "Section A-A-1")->status);
        self::assertEquals(EventObjectInfo::$BOOKED, $this->seatsioClient->events->retrieveObjectInfo($event->key, "Section A-A-2")->status);

        $a1ObjectInfo = $res->objects["Section A-A-1"];
        self::assertEquals("Section A", $a1ObjectInfo->section);
        self::assertEquals("Entrance 1", $a1ObjectInfo->entrance);
        self::assertNull($a1ObjectInfo->floor);
        self::assertEquals(someLabels("1", "seat", "A", "row", "Section A"), $a1ObjectInfo->labels);
        self::assertEquals(new IDs("1", "A", "Section A"), $a1ObjectInfo->ids);
    }

    public function testHoldToken()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey);
        $holdToken = $this->seatsioClient->holdTokens->create();
        $this->seatsioClient->events->hold($event->key, "A-1", $holdToken->holdToken);

        $this->seatsioClient->events->book($event->key, "A-1", $holdToken->holdToken);

        $objectInfo = $this->seatsioClient->events->retrieveObjectInfo($event->key, "A-1");
        self::assertEquals(EventObjectInfo::$BOOKED, $objectInfo->status);
        self::assertNull($objectInfo->holdToken);
    }

    public function testOrderId()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey);

        $this->seatsioClient->events->book($event->key, "A-1", null, "order1");

        $objectInfo = $this->seatsioClient->events->retrieveObjectInfo($event->key, "A-1");
        self::assertEquals("order1", $objectInfo->orderId);
    }

    public function testKeepExtraData()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey);
        $extraData = ["foo" => "bar"];
        $this->seatsioClient->events->updateExtraData($event->key, "A-1", $extraData);

        $this->seatsioClient->events->book($event->key, "A-1", null, null, true);

        $objectInfo = $this->seatsioClient->events->retrieveObjectInfo($event->key, "A-1");
        self::assertEquals((object)$extraData, $objectInfo->extraData);
    }

    public function testChannelKeys()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey, (new CreateEventParams())->setChannels([
            new Channel("channelKey1", "channel 1", "#FF0000", 1, ["A-1", "A-2"])
        ]));

        $this->seatsioClient->events->book($event->key, "A-1", null, null, null, null, ["channelKey1"]);

        $objectInfo = $this->seatsioClient->events->retrieveObjectInfo($event->key, "A-1");
        self::assertEquals(EventObjectInfo::$BOOKED, $objectInfo->status);
    }

    public function testIgnoreChannels()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey, (new CreateEventParams())->setChannels([
            new Channel("channelKey1", "channel 1", "#FF0000", 1, ["A-1", "A-2"])
        ]));

        $this->seatsioClient->events->book($event->key, "A-1", null, null, null, true);

        $objectInfo = $this->seatsioClient->events->retrieveObjectInfo($event->key, "A-1");
        self::assertEquals(EventObjectInfo::$BOOKED, $objectInfo->status);
    }
}
