<?php

namespace Seatsio\Events\Channels;

use Seatsio\Events\Channel;
use Seatsio\SeatsioClientTest;

class AddObjectsToChannelTest extends SeatsioClientTest
{

    public function test()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey);

        $this->seatsioClient->events->channels->add($event->key, "channelKey1", "channel 1", "#FFFF98", 1, ["A-1", "A-2"]);
        $this->seatsioClient->events->channels->add($event->key, "channelKey2", "channel 2", "#FFFF99", 2, ["A-3", "A-4"]);

        $this->seatsioClient->events->channels->addObjects($event->key, "channelKey1", ["A-3", "A-4"]);

        $retrievedEvent = $this->seatsioClient->events->retrieve($event->key);

        self::assertEquals([
            new Channel("channel 1", "#FFFF98", 1, "channelKey1", ["A-1", "A-2", "A-3", "A-4"]),
            new Channel("channel 2", "#FFFF99", 2, "channelKey2", [])
        ], $retrievedEvent->channels);
    }

}
