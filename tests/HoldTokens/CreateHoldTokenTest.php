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
        self::assertGreaterThanOrEqual(14 * 60, $holdToken->expiresInSeconds);
        self::assertLessThanOrEqual(15 * 60, $holdToken->expiresInSeconds);
    }

    public function testExpiresInMinutes()
    {
        $lowerBound = (new DateTime('UTC'))->add(new DateInterval('PT5M'));
        $upperBound = (new DateTime('UTC'))->add(new DateInterval('PT6M'));

        $holdToken = $this->seatsioClient->holdTokens->create(5);

        self::assertNotEmpty($holdToken->holdToken);
        self::assertGreaterThanOrEqual($lowerBound, $holdToken->expiresAt);
        self::assertLessThanOrEqual($upperBound, $holdToken->expiresAt);
        self::assertGreaterThanOrEqual(4 * 60, $holdToken->expiresInSeconds);
        self::assertLessThanOrEqual(5 * 60, $holdToken->expiresInSeconds);
    }

}