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
        $channels = $this->seatsioClient->events->retrieve($event->key)->channels;
        self::assertEquals([
            new Channel("channelKey1", $channels[0]->id, "channel 2", "#FFFF98", 1, ["A-1", "A-2"], []),
        ], $channels);
    }

    public function testUpdateColor()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey);
        $this->seatsioClient->events->channels->add($event->key, "channelKey1", "channel 1", "#FFFF98", 1, ["A-1", "A-2"]);
        $this->seatsioClient->events->channels->update($event->key, "channelKey1", null, "#FFAABB");
        $channels = $this->seatsioClient->events->retrieve($event->key)->channels;
        self::assertEquals([
            new Channel("channelKey1", $channels[0]->id, "channel 1", "#FFAABB", 1, ["A-1", "A-2"], []),
        ], $channels);
    }

    public function testUpdateObjects()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey);
        $this->seatsioClient->events->channels->add($event->key, "channelKey1", "channel 1", "#FFFF98", 1, ["A-1", "A-2"]);
        $this->seatsioClient->events->channels->update($event->key, "channelKey1", null, null, ["A-3"]);
        $channels = $this->seatsioClient->events->retrieve($event->key)->channels;
        self::assertEquals([
            new Channel("channelKey1", $channels[0]->id, "channel 1", "#FFFF98", 1, ["A-3"], []),
        ], $channels);
    }

    public function testUpdateAreaPlaces()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey);
        $this->seatsioClient->events->channels->add($event->key, "channelKey1", "channel 1", "#FFFF98", 1, ["A-1"]);
        $this->seatsioClient->events->channels->update($event->key, "channelKey1", null, null, null, ["GA1" => 5]);
        $channels = $this->seatsioClient->events->retrieve($event->key)->channels;
        self::assertEquals([
            new Channel("channelKey1", $channels[0]->id, "channel 1", "#FFFF98", 1, ["A-1"], ["GA1" => 5]),
        ], $channels);
    }

    public function testClearAreaPlaces()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey);
        $this->seatsioClient->events->channels->add($event->key, "channelKey1", "channel 1", "#FFFF98", 1, ["A-1"], ["GA1" => 5]);
        $this->seatsioClient->events->channels->update($event->key, "channelKey1", null, null, null, []);
        $channels = $this->seatsioClient->events->retrieve($event->key)->channels;
        self::assertEquals([
            new Channel("channelKey1", $channels[0]->id, "channel 1", "#FFFF98", 1, ["A-1"], []),
        ], $channels);
    }
}
