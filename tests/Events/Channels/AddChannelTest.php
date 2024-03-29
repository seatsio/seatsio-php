<?php

namespace Seatsio\Events\Channels;

use Seatsio\Events\Channel;
use Seatsio\Events\ChannelCreationParams;
use Seatsio\SeatsioClientTest;

class AddChannelTest extends SeatsioClientTest
{

    public function testAddChannel()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey);

        $this->seatsioClient->events->channels->add($event->key, "channelKey1", "channel 1", "#FFFF98", 1, ["A-1", "A-2"]);
        $this->seatsioClient->events->channels->add($event->key, "channelKey2", "channel 2", "#FFFF99", 2, ["A-3"]);

        $retrievedEvent = $this->seatsioClient->events->retrieve($event->key);

        self::assertEquals([
            new Channel("channelKey1", "channel 1", "#FFFF98", 1, ["A-1", "A-2"]),
            new Channel("channelKey2", "channel 2", "#FFFF99", 2, ["A-3"])
        ], $retrievedEvent->channels);
    }

    public function testAddMultipleChannels()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey);

        $this->seatsioClient->events->channels->addMultiple(
            $event->key,
            [
                (new ChannelCreationParams())->setChannelKey("channelKey1")->setName("channel 1")->setColor("#FFFF98")->setIndex(1)->setObjects(["A-1", "A-2"]),
                (new ChannelCreationParams())->setChannelKey("channelKey2")->setName("channel 2")->setColor("#FFFF99")->setIndex(2)->setObjects(["A-3"])
            ]
        );

        $retrievedEvent = $this->seatsioClient->events->retrieve($event->key);

        self::assertEquals([
            new Channel("channelKey1", "channel 1", "#FFFF98", 1, ["A-1", "A-2"]),
            new Channel("channelKey2", "channel 2", "#FFFF99", 2, ["A-3"])
        ], $retrievedEvent->channels);
    }

    public function testIndexIsOptional()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey);

        $this->seatsioClient->events->channels->add($event->key, "channelKey1", "channel 1", "#FFFF98", null, ["A-1", "A-2"]);

        $retrievedEvent = $this->seatsioClient->events->retrieve($event->key);

        self::assertEquals([
            new Channel("channelKey1", "channel 1", "#FFFF98", 0, ["A-1", "A-2"]),
        ], $retrievedEvent->channels);
    }

    public function testObjectsAreOptional()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey);

        $this->seatsioClient->events->channels->add($event->key, "channelKey1", "channel 1", "#FFFF98", 1, null);

        $retrievedEvent = $this->seatsioClient->events->retrieve($event->key);

        self::assertEquals([
            new Channel("channelKey1", "channel 1", "#FFFF98", 1, []),
        ], $retrievedEvent->channels);
    }

}
