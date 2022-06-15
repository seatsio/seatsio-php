<?php

namespace Seatsio\Events\Channels;

use Seatsio\Events\Channel;
use Seatsio\SeatsioClientTest;

class UpdateChannelTest extends SeatsioClientTest
{

    public function testUpdateName()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey);
        $this->seatsioClient->events->channels->add($event->key, "channelKey1", "channel 1", "#FFFF98", 1, ["A-1", "A-2"]);

        $this->seatsioClient->events->channels->update($event->key, "channelKey1", "channel 2");

        $retrievedEvent = $this->seatsioClient->events->retrieve($event->key);
        self::assertEquals([
            new Channel("channel 2", "#FFFF98", 1, "channelKey1", ["A-1", "A-2"]),
        ], $retrievedEvent->channels);
    }

    public function testUpdateColor()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey);
        $this->seatsioClient->events->channels->add($event->key, "channelKey1", "channel 1", "#FFFF98", 1, ["A-1", "A-2"]);

        $this->seatsioClient->events->channels->update($event->key, "channelKey1", null, "#FFAABB");

        $retrievedEvent = $this->seatsioClient->events->retrieve($event->key);
        self::assertEquals([
            new Channel("channel 1", "#FFAABB", 1, "channelKey1", ["A-1", "A-2"]),
        ], $retrievedEvent->channels);
    }

    public function testUpdateObjects()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey);
        $this->seatsioClient->events->channels->add($event->key, "channelKey1", "channel 1", "#FFFF98", 1, ["A-1", "A-2"]);

        $this->seatsioClient->events->channels->update($event->key, "channelKey1", null, null, ["A-3"]);

        $retrievedEvent = $this->seatsioClient->events->retrieve($event->key);
        self::assertEquals([
            new Channel("channel 1", "#FFFF98", 1, "channelKey1", ["A-3"]),
        ], $retrievedEvent->channels);
    }

}
