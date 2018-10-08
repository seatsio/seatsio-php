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
        self::assertNotEmpty($account->publicKey);
        self::assertNotEmpty($account->email);
        self::assertTrue($account->settings->draftChartDrawingsEnabled);
        self::assertEquals("ERROR", $account->settings->chartValidation->VALIDATE_DUPLICATE_LABELS);
        self::assertEquals("ERROR", $account->settings->chartValidation->VALIDATE_OBJECTS_WITHOUT_CATEGORIES);
        self::assertEquals("ERROR", $account->settings->chartValidation->VALIDATE_UNLABELED_OBJECTS);
    }
}