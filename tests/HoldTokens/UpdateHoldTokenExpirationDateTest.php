<?php

namespace Seatsio\HoldTokens;

use Seatsio\SeatsioClientTest;

class UpdateHoldTokenExpirationDateTest extends SeatsioClientTest
{

    public function test()
    {
        $holdToken = $this->seatsioClient->holdTokens->create();

        $updatedHoldToken = $this->seatsioClient->holdTokens->expireInMinutes($holdToken->holdToken, 30);

        self::assertEquals($holdToken->holdToken, $updatedHoldToken->holdToken);
        self::assertNotEquals($holdToken->expiresAt, $updatedHoldToken->expiresAt);
    }

}