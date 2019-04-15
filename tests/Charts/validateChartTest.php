<?php

namespace Seatsio\Charts;

use Seatsio\SeatsioClientTest;

class ValidateChartTest extends SeatsioClientTest
{
    public function testValidatePublishedVersion()
    {
        $chartKey = $this->createTestChartWithErrors();

        $validationErrors = $this->seatsioClient->charts->validatePublishedVersion($chartKey);

        $errors = \Functional\map($validationErrors->errors, function($error) { return $error->validatorKey; });
        self::assertEquals(['VALIDATE_UNLABELED_OBJECTS', 'VALIDATE_DUPLICATE_LABELS', 'VALIDATE_OBJECTS_WITHOUT_CATEGORIES'], $errors, '', 0.0, 10, true);
    }
}
