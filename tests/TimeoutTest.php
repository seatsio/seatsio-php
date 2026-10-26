<?php

namespace Seatsio;

use PHPUnit\Framework\TestCase;

class TimeoutTest extends TestCase
{
    public function testDefaultTimeouts()
    {
        $client = new SeatsioClient(Region::EU(), 'aSecretKey');

        self::assertEquals(30, $client->client->getConfig('timeout'));
        self::assertEquals(10, $client->client->getConfig('connect_timeout'));
    }

    public function testCustomTimeouts()
    {
        $client = new SeatsioClient(Region::EU(), 'aSecretKey', null, 5, 60, 2.5);

        self::assertEquals(60, $client->client->getConfig('timeout'));
        self::assertEquals(2.5, $client->client->getConfig('connect_timeout'));
    }
}
