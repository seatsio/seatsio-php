<?php

namespace Seatsio\Events\Channels;

use Seatsio\Events\Channel;
use Seatsio\SeatsioClientTest;

class RemoveChannelTest extends SeatsioClientTest
{

    public function test()
    {

        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey);

        $this->seatsioClient->events->channels->replace($event->key, [
            "channelKey1" => new Channel("channel 1", "#FF0000", 1),
            "channelKey2" => new Channel("channel 2", "#00FFFF", 2)
        ]);

        $this->seatsioClient->events->channels->remove($event->key, "channelKey2");

        $retrievedEvent = $this->seatsioClient->events->retrieve($event->key);

        self::assertEquals([
            new Channel("channel 1", "#FF0000", 1, "channelKey1", []),
        ], $retrievedEvent->channels);
    }

}
