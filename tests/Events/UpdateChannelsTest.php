<?php

namespace Seatsio\Events;

use Seatsio\SeatsioClientTest;

class UpdateChannelsTest extends SeatsioClientTest
{

    public function test()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey);

        $this->seatsioClient->events->updateChannels($event->key, [
            "channelKey1" => new Channel("channel 1", "#FF0000", 1),
            "channelKey2" => new Channel("channel 2", "#00FFFF", 2)
        ]);

        $retrievedEvent = $this->seatsioClient->events->retrieve($event->key);

        self::assertEquals([
            new Channel("channel 1", "#FF0000", 1, "channelKey1", []),
            new Channel("channel 2", "#00FFFF", 2, "channelKey2", [])
        ], $retrievedEvent->channels);

    }

}
