<?php

namespace Seatsio\HoldTokens;

use Seatsio\SeatsioClientTest;

class CreateHoldTokenTest extends SeatsioClientTest
{

    public function test()
    {
        $holdToken = $this->seatsioClient->holdTokens->create();

        self::assertNotEmpty($holdToken->holdToken);
        self::assertNotEmpty($holdToken->expiresAt);
    }

}