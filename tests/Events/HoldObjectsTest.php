<?php

namespace Seatsio\Events;

use Seatsio\SeatsioClientTest;

class HoldObjectsTest extends SeatsioClientTest
{

    public function test()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey);
        $holdToken = $this->seatsioClient->holdTokens->create();

        $res = $this->seatsioClient->events->hold($event->key, ["A-1", "A-2"], $holdToken->holdToken);

        $status1 = $this->seatsioClient->events->retrieveObjectStatus($event->key, "A-1");
        self::assertEquals(ObjectStatus::$HELD, $status1->status);
        self::assertEquals($holdToken->holdToken, $status1->holdToken);

        $status2 = $this->seatsioClient->events->retrieveObjectStatus($event->key, "A-2");
        self::assertEquals(ObjectStatus::$HELD, $status2->status);
        self::assertEquals($holdToken->holdToken, $status2->holdToken);

        self::assertEquals(["A-1", "A-2"], array_keys($res->objects));
    }

    public function testOrderId()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey);
        $holdToken = $this->seatsioClient->holdTokens->create();

        $this->seatsioClient->events->hold($event->key, "A-1", $holdToken->holdToken, "order1");

        $objectStatus = $this->seatsioClient->events->retrieveObjectStatus($event->key, "A-1");
        self::assertEquals("order1", $objectStatus->orderId);
    }

    public function testKeepExtraData()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey);
        $extraData = ["foo" => "bar"];
        $this->seatsioClient->events->updateExtraData($event->key, "A-1", $extraData);
        $holdToken = $this->seatsioClient->holdTokens->create();

        $this->seatsioClient->events->hold($event->key, "A-1", $holdToken->holdToken, null, true);

        $objectStatus = $this->seatsioClient->events->retrieveObjectStatus($event->key, "A-1");
        self::assertEquals((object)$extraData, $objectStatus->extraData);
    }

    public function testAcceptChannelKeys()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey);
        $holdToken = $this->seatsioClient->holdTokens->create();
        $this->seatsioClient->events->updateChannels($event->key, [
            "channelKey1" => new Channel("channel 1", "#FF0000", 1)
        ]);
        $this->seatsioClient->events->assignObjectsToChannels($event->key, [
            "channelKey1" => ["A-1", "A-2"]
        ]);

        $this->seatsioClient->events->hold($event->key, "A-1", $holdToken->holdToken, null, null, ["channelKey1"]);

        $objectStatus = $this->seatsioClient->events->retrieveObjectStatus($event->key, "A-1");
        self::assertEquals(ObjectStatus::$HELD, $objectStatus->status);
    }


}
