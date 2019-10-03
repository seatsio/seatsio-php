<?php

namespace Seatsio\HoldTokens;

use Seatsio\SeatsioClientTest;

class RetrieveHoldTokenTest extends SeatsioClientTest
{

    public function test()
    {
        $holdToken = $this->seatsioClient->holdTokens->create();

        $retrievedHoldToken = $this->seatsioClient->holdTokens->retrieve($holdToken->holdToken);

        self::assertEquals($holdToken->holdToken, $retrievedHoldToken->holdToken);
        self::assertEquals($holdToken->expiresAt, $retrievedHoldToken->expiresAt);
        self::assertGreaterThanOrEqual(14 * 60, $holdToken->expiresInSeconds);
        self::assertLessThanOrEqual(15 * 60, $holdToken->expiresInSeconds);
        self::assertNotNull($holdToken->workspaceKey);
    }

}
