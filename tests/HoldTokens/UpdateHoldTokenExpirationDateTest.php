<?php

namespace Seatsio\HoldTokens;

use DateInterval;
use DateTime;
use Seatsio\SeatsioClientTest;

class UpdateHoldTokenExpirationDateTest extends SeatsioClientTest
{

    public function test()
    {
        $lowerBound = (new DateTime('UTC'))->add(new DateInterval('PT30M'));
        $upperBound = (new DateTime('UTC'))->add(new DateInterval('PT31M'));

        $holdToken = $this->seatsioClient->holdTokens->create();

        $updatedHoldToken = $this->seatsioClient->holdTokens->expireInMinutes($holdToken->holdToken, 30);

        self::assertEquals($holdToken->holdToken, $updatedHoldToken->holdToken);
        self::assertGreaterThanOrEqual($lowerBound, $updatedHoldToken->expiresAt);
        self::assertLessThanOrEqual($upperBound, $updatedHoldToken->expiresAt);
    }

}