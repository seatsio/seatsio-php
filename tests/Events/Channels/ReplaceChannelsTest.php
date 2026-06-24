<?php

namespace Seatsio\Events\Channels;

use Seatsio\Events\Channel;
use Seatsio\Events\ChannelCreationParams;
use Seatsio\SeatsioClientTest;

class ReplaceChannelsTest extends SeatsioClientTest
{

    public function test()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey);

        $this->seatsioClient->events->channels->replace($event->key, [
            (new ChannelCreationParams())->setChannelKey("channelKey1")->setName("channel 1")->setColor("#FF0000")->setIndex(1)->setObjects(["A-1", "A-2"])->setAreaPlaces(["GA1" => 3]),
            (new ChannelCreationParams())->setChannelKey("channelKey2")->setName("channel 2")->setColor("#00FFFF")->setIndex(2)->setObjects([])
        ]);

        $retrievedEvent = $this->seatsioClient->events->retrieve($event->key);

        $channels = $retrievedEvent->channels;

        self::assertEquals([
            new Channel("channelKey1", $channels[0]->id, "channel 1", "#FF0000", 1, ["A-1", "A-2"], ["GA1" => 3]),
            new Channel("channelKey2", $channels[1]->id, "channel 2", "#00FFFF", 2, [], [])
        ], $channels);
    }

    public function testReplaceWithAssociativeArrayKeys()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey);

        $this->seatsioClient->events->channels->replace($event->key, [
            "channelKey1" => (new ChannelCreationParams())->setChannelKey("channelKey1")->setName("channel 1")->setColor("#FF0000")->setIndex(1)->setObjects(["A-1", "A-2"])->setAreaPlaces(["GA1" => 3]),
            "channelKey2" => (new ChannelCreationParams())->setChannelKey("channelKey2")->setName("channel 2")->setColor("#00FFFF")->setIndex(2)->setObjects([])
        ]);

        $retrievedEvent = $this->seatsioClient->events->retrieve($event->key);

        $channels = $retrievedEvent->channels;

        self::assertEquals([
            new Channel("channelKey1", $channels[0]->id, "channel 1", "#FF0000", 1, ["A-1", "A-2"], ["GA1" => 3]),
            new Channel("channelKey2", $channels[1]->id, "channel 2", "#00FFFF", 2, [], [])
        ], $channels);
    }

}
