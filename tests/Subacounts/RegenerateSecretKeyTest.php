<?php

namespace Seatsio\Subaccounts;

use Seatsio\SeatsioClientTest;

class RegenerateSecretKeyTest extends SeatsioClientTest
{

    public function test()
    {
        $subaccount = $this->seatsioClient->subaccounts->create();

        $newSecretKey = $this->seatsioClient->subaccounts->regenerateSecretKey($subaccount->id);

        self::assertNotNull($newSecretKey);
        self::assertNotEquals($subaccount->secretKey, $newSecretKey);
    }
}