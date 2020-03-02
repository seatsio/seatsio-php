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
}
