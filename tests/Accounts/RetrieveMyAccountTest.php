<?php

namespace Seatsio\Accounts;

use Seatsio\SeatsioClientTest;

class RetrieveMyAccountTest extends SeatsioClientTest
{

    public function test()
    {
        $account = $this->seatsioClient->accounts->retrieveMyAccount();

        self::assertNotEmpty($account->secretKey);
        self::assertNotEmpty($account->designerKey);
        self::assertNotEmpty($account->email);
        self::assertTrue($account->settings->draftChartDrawingsEnabled);
        self::assertTrue($account->settings->holdOnSelectForGAs);
        self::assertEquals("OFF", $account->settings->chartValidation->VALIDATE_FOCAL_POINT);
    }
}
