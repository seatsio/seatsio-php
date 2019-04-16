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
        self::assertEquals(['VALIDATE_UNLABELED_OBJECTS', 'VALIDATE_DUPLICATE_LABELS', 'VALIDATE_OBJECTS_WITHOUT_CATEGORIES'], $validationRes->errors, '', 0.0, 10, true);
    }
}
