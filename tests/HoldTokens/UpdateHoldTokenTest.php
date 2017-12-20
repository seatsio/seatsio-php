<?php

namespace Seatsio\HoldTokens;

use Seatsio\SeatsioClientTest;

class UpdateHoldTokenTest extends SeatsioClientTest
{

    public function test()
    {
        $holdToken = $this->seatsioClient->holdTokens()->create();

        $updatedHoldToken = $this->seatsioClient->holdTokens()->update($holdToken->holdToken, 30);

        self::assertEquals($holdToken->holdToken, $updatedHoldToken->holdToken);
        self::assertNotEquals($holdToken->expiresAt, $updatedHoldToken->expiresAt);
    }

}