<?php

namespace Seatsio\Events;

use Seatsio\SeatsioClientTest;

class ChangeObjectStatusInBatchTest extends SeatsioClientTest
{

    public function test()
    {
        $chartKey1 = $this->createTestChart();
        $chartKey2 = $this->createTestChart();
        $event1 = $this->seatsioClient->events->create($chartKey1);
        $event2 = $this->seatsioClient->events->create($chartKey2);

        $response = $this->seatsioClient->events->changeObjectStatusInBatch([
            new StatusChangeRequest($event1->key, "A-1", "lolzor"),
            new StatusChangeRequest($event2->key, "A-2", "lolzor")
        ]);

        self::assertEquals('lolzor', $response[0]->objects['A-1']->status);
        $objectInfo1 = $this->seatsioClient->events->retrieveObjectInfo($event1->key, "A-1");
        self::assertEquals("lolzor", $objectInfo1->status);

        self::assertEquals('lolzor', $response[1]->objects['A-2']->status);
        $objectInfo2 = $this->seatsioClient->events->retrieveObjectInfo($event2->key, "A-2");
        self::assertEquals("lolzor", $objectInfo2->status);
    }

    public function testChannelKeys()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey);
        $this->seatsioClient->events->updateChannels($event->key, [
            "channelKey1" => new Channel("channel 1", "#FF0000", 1)
        ]);
        $this->seatsioClient->events->assignObjectsToChannels($event->key, [
            "channelKey1" => ["A-1"]
        ]);

        $response = $this->seatsioClient->events->changeObjectStatusInBatch([
            new StatusChangeRequest($event->key, "A-1", "lolzor", null, null, null, null, ["channelKey1"])
        ]);

        self::assertEquals('lolzor', $response[0]->objects['A-1']->status);
    }

    public function testIgnoreChannels()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey);
        $this->seatsioClient->events->updateChannels($event->key, [
            "channelKey1" => new Channel("channel 1", "#FF0000", 1)
        ]);
        $this->seatsioClient->events->assignObjectsToChannels($event->key, [
            "channelKey1" => ["A-1"]
        ]);

        $response = $this->seatsioClient->events->changeObjectStatusInBatch([
            new StatusChangeRequest($event->key, "A-1", "lolzor", null, null, null, true)
        ]);

        self::assertEquals('lolzor', $response[0]->objects['A-1']->status);
    }
}
