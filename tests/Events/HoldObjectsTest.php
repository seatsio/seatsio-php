<?php

namespace Seatsio\Events;

use Seatsio\Charts\SocialDistancingRuleset;
use Seatsio\SeatsioClientTest;

class HoldObjectsTest extends SeatsioClientTest
{

    public function test()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey);
        $holdToken = $this->seatsioClient->holdTokens->create();

        $res = $this->seatsioClient->events->hold($event->key, ["A-1", "A-2"], $holdToken->holdToken);

        $objectInfo1 = $this->seatsioClient->events->retrieveObjectInfo($event->key, "A-1");
        self::assertEquals(EventObjectInfo::$HELD, $objectInfo1->status);
        self::assertEquals($holdToken->holdToken, $objectInfo1->holdToken);

        $objectInfo2 = $this->seatsioClient->events->retrieveObjectInfo($event->key, "A-2");
        self::assertEquals(EventObjectInfo::$HELD, $objectInfo2->status);
        self::assertEquals($holdToken->holdToken, $objectInfo2->holdToken);

        self::assertEquals(["A-1", "A-2"], SeatsioClientTest::sort(array_keys($res->objects)));
    }

    public function testOrderId()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey);
        $holdToken = $this->seatsioClient->holdTokens->create();

        $this->seatsioClient->events->hold($event->key, "A-1", $holdToken->holdToken, "order1");

        $objectInfo = $this->seatsioClient->events->retrieveObjectInfo($event->key, "A-1");
        self::assertEquals("order1", $objectInfo->orderId);
    }

    public function testKeepExtraData()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey);
        $extraData = ["foo" => "bar"];
        $this->seatsioClient->events->updateExtraData($event->key, "A-1", $extraData);
        $holdToken = $this->seatsioClient->holdTokens->create();

        $this->seatsioClient->events->hold($event->key, "A-1", $holdToken->holdToken, null, true);

        $objectInfo = $this->seatsioClient->events->retrieveObjectInfo($event->key, "A-1");
        self::assertEquals((object)$extraData, $objectInfo->extraData);
    }

    public function testChannelKeys()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey);
        $holdToken = $this->seatsioClient->holdTokens->create();
        $this->seatsioClient->events->channels->replace($event->key, [
            new Channel("channelKey1", "channel 1", "#FF0000", 1, ["A-1", "A-2"])
        ]);

        $this->seatsioClient->events->hold($event->key, "A-1", $holdToken->holdToken, null, null, null, ["channelKey1"]);

        $objectInfo = $this->seatsioClient->events->retrieveObjectInfo($event->key, "A-1");
        self::assertEquals(EventObjectInfo::$HELD, $objectInfo->status);
    }

    public function testIgnoreChannels()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey);
        $holdToken = $this->seatsioClient->holdTokens->create();
        $this->seatsioClient->events->channels->replace($event->key, [
            new Channel("channelKey1", "channel 1", "#FF0000", 1, ["A-1", "A-2"])
        ]);

        $this->seatsioClient->events->hold($event->key, "A-1", $holdToken->holdToken, null, null, true);

        $objectInfo = $this->seatsioClient->events->retrieveObjectInfo($event->key, "A-1");
        self::assertEquals(EventObjectInfo::$HELD, $objectInfo->status);
    }

    public function testIgnoreSocialDistancing()
    {
        $chartKey = $this->createTestChart();
        $holdToken = $this->seatsioClient->holdTokens->create();
        $ruleset = SocialDistancingRuleset::fixed("ruleset")->setDisabledSeats(["A-1"])->build();
        $this->seatsioClient->charts->saveSocialDistancingRulesets($chartKey, ["ruleset" => $ruleset]);
        $event = $this->seatsioClient->events->create($chartKey, CreateEventParams::create()->setSocialDistancingRulesetKey("ruleset"));

        $this->seatsioClient->events->hold($event->key, "A-1", $holdToken->holdToken, null, null, null, null, true);

        $objectInfo = $this->seatsioClient->events->retrieveObjectInfo($event->key, "A-1");
        self::assertEquals(EventObjectInfo::$HELD, $objectInfo->status);
    }
}
