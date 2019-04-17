<?php

namespace Seatsio\Accounts;

use Seatsio\SeatsioClientTest;

class UpdateSetting extends SeatsioClientTest
{
    public function test()
    {

        $this->seatsioClient->accounts->updateSetting('VALIDATE_DUPLICATE_LABELS', 'WARNING');
        $this->seatsioClient->accounts->updateSetting('VALIDATE_OBJECT_TYPES_PER_CATEGORY', 'ERROR');

        $account = $this->seatsioClient->accounts->retrieveMyAccount();
        self::assertEquals("WARNING", $account->settings->chartValidation->VALIDATE_DUPLICATE_LABELS);
        self::assertEquals("ERROR", $account->settings->chartValidation->VALIDATE_OBJECTS_WITHOUT_CATEGORIES);
        self::assertEquals("ERROR", $account->settings->chartValidation->VALIDATE_UNLABELED_OBJECTS);
        self::assertEquals("OFF", $account->settings->chartValidation->VALIDATE_FOCAL_POINT);
        self::assertEquals("ERROR", $account->settings->chartValidation->VALIDATE_OBJECT_TYPES_PER_CATEGORY);
    }
}
