<?php

namespace Seatsio\Charts;

use Seatsio\SeatsioClientTest;

class ValidateChartTest extends SeatsioClientTest
{
    public function testValidatePublishedVersion()
    {
        $chartKey = $this->createTestChartWithErrors();

        $validationRes = $this->seatsioClient->charts->validatePublishedVersion($chartKey);

        self::assertEmpty($validationRes->warnings);
        self::assertEmpty($validationRes->errors);
    }

    public function testValidateDraftVersion()
    {
        $this->seatsioClient->accounts->updateSetting('VALIDATE_DUPLICATE_LABELS', 'WARNING');
        $this->seatsioClient->accounts->updateSetting('VALIDATE_UNLABELED_OBJECTS', 'WARNING');
        $this->seatsioClient->accounts->updateSetting('VALIDATE_OBJECTS_WITHOUT_CATEGORIES', 'WARNING');
        $this->seatsioClient->accounts->updateSetting('VALIDATE_FOCAL_POINT', 'WARNING');
        $this->seatsioClient->accounts->updateSetting('VALIDATE_OBJECT_TYPES_PER_CATEGORY', 'WARNING');

        $chartKey = $this->createTestChartWithErrors();
        $this->seatsioClient->events->create($chartKey);
        $this->seatsioClient->charts->update($chartKey, 'New name');

        $validationRes = $this->seatsioClient->charts->validateDraftVersion($chartKey);

        self::assertEmpty($validationRes->errors);
        self::assertEquals(['VALIDATE_UNLABELED_OBJECTS', 'VALIDATE_DUPLICATE_LABELS', 'VALIDATE_OBJECTS_WITHOUT_CATEGORIES', 'VALIDATE_FOCAL_POINT', 'VALIDATE_OBJECT_TYPES_PER_CATEGORY'], $validationRes->warnings, '', 0.0, 10, true);
    }
}
