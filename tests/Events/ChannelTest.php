<?php

namespace Seatsio\Events;

use PHPUnit\Framework\TestCase;

class ChannelTest extends TestCase
{

    public function testAreaPartitionLabel()
    {
        $channel = new Channel("channelKey1", "abc123", "channel 1", "#FF0000", 1, [], []);

        self::assertEquals("myArea##abc123", $channel->areaPartitionLabel("myArea"));
    }

}

