<?php

namespace Seatsio\Events\Channels;

use Seatsio\Events\Channel;
use Seatsio\SeatsioClientTest;

class RemoveObjectsFromChannelTest extends SeatsioClientTest
{

    public function test()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey);
        $this->seatsioClient->events->channels->add($event->key, "channelKey1", "channel 1", "#FFFF98", 1, ["A-1", "A-2", "A-3", "A-4"]);

        $this->seatsioClient->events->channels->removeObjects($event->key, "channelKey1", ["A-3", "A-4"]);

        $retrievedEvent = $this->seatsioClient->events->retrieve($event->key);

        self::assertEquals([
            new Channel("channelKey1", "channel 1", "#FFFF98", 1, ["A-1", "A-2"]),
        ], $retrievedEvent->channels);
    }

}
