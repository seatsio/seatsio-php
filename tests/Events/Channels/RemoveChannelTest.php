<?php

namespace Seatsio\Events\Channels;

use Seatsio\Events\Channel;
use Seatsio\Events\ChannelCreationParams;
use Seatsio\Events\CreateEventParams;
use Seatsio\SeatsioClientTest;

class RemoveChannelTest extends SeatsioClientTest
{

    public function test()
    {

        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey, (new CreateEventParams())->setChannels([
            (new ChannelCreationParams())->setChannelKey("channelKey1")->setName("channel 1")->setColor("#FF0000")->setIndex(1)->setObjects(["A-1", "A-2"])->setAreaPlaces(["GA1" => 3]),
            (new ChannelCreationParams())->setChannelKey("channelKey2")->setName("channel 2")->setColor("#FF0000")->setIndex(2)->setObjects([])
        ]));

        $this->seatsioClient->events->channels->remove($event->key, "channelKey2");

        $retrievedEvent = $this->seatsioClient->events->retrieve($event->key);

        $channels = $retrievedEvent->channels;

        self::assertEquals([
            new Channel("channelKey1", $channels[0]->id, "channel 1", "#FF0000", 1, ["A-1", "A-2"], ["GA1" => 3]),
        ], $channels);
    }

}
