<?php

namespace Seatsio\HoldTokens;

use DateInterval;
use DateTime;
use Seatsio\SeatsioClientTest;

class CreateHoldTokenTest extends SeatsioClientTest
{

    public function test()
    {
        $lowerBound = (new DateTime('UTC'))->add(new DateInterval('PT15M'));
        $upperBound = (new DateTime('UTC'))->add(new DateInterval('PT16M'));

        $holdToken = $this->seatsioClient->holdTokens->create();

        self::assertNotEmpty($holdToken->holdToken);
        self::assertGreaterThanOrEqual($lowerBound, $holdToken->expiresAt);
        self::assertLessThanOrEqual($upperBound, $holdToken->expiresAt);
    }

    public function testExpiresInMinutes()
    {
        $lowerBound = (new DateTime('UTC'))->add(new DateInterval('PT5M'));
        $upperBound = (new DateTime('UTC'))->add(new DateInterval('PT6M'));

        $holdToken = $this->seatsioClient->holdTokens->create(5);

        self::assertNotEmpty($holdToken->holdToken);
        self::assertGreaterThanOrEqual($lowerBound, $holdToken->expiresAt);
        self::assertLessThanOrEqual($upperBound, $holdToken->expiresAt);
    }

}