<?php

namespace Seatsio\Events\Channels;

use Seatsio\Events\Channel;
use Seatsio\Events\CreateEventParams;
use Seatsio\SeatsioClientTest;

class RemoveChannelTest extends SeatsioClientTest
{

    public function test()
    {

        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey, (new CreateEventParams())->setChannels([
            new Channel("channelKey1", "channel 1", "#FF0000", 1, ["A-1", "A-2"]),
            new Channel("channelKey2", "channel 2", "#FF0000", 2, [])
        ]));

        $this->seatsioClient->events->channels->remove($event->key, "channelKey2");

        $retrievedEvent = $this->seatsioClient->events->retrieve($event->key);

        self::assertEquals([
            new Channel("channelKey1", "channel 1", "#FF0000", 1, ["A-1", "A-2"]),
        ], $retrievedEvent->channels);
    }

}
