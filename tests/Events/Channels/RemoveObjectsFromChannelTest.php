<?php

namespace Seatsio\Events\Channels;

use Seatsio\Events\Channel;
use Seatsio\SeatsioClientTest;

class RemoveObjectsFromChannelTest extends SeatsioClientTest
{

    public function testRemoveObjectsFromChannel()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey);
        $this->seatsioClient->events->channels->add($event->key, "channelKey1", "channel 1", "#FFFF98", 1, ["A-1", "A-2", "A-3", "A-4"]);

        $this->seatsioClient->events->channels->removeObjects($event->key, "channelKey1", ["A-3", "A-4"]);

        $channels = $this->seatsioClient->events->retrieve($event->key)->channels;

        self::assertEquals([
            new Channel("channelKey1", $channels[0]->id, "channel 1", "#FFFF98", 1, ["A-1", "A-2"], []),
        ], $channels);
    }

    public function testRemoveAreaPlacesFromChannel()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey);
        $this->seatsioClient->events->channels->add($event->key, "channelKey1", "channel 1", "#FFFF98", 1, ["A-1"], ["GA1" => 3]);

        $this->seatsioClient->events->channels->removeObjects($event->key, "channelKey1", [], ["GA1" => 1]);

        $channels = $this->seatsioClient->events->retrieve($event->key)->channels;

        self::assertEquals([
            new Channel("channelKey1", $channels[0]->id, "channel 1", "#FFFF98", 1, ["A-1"], ["GA1" => 2]),
        ], $channels);
    }

}
