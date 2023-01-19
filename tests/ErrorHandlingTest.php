<?php

namespace Seatsio;

class ErrorHandlingTest extends SeatsioClientTest
{

    public function test4xx()
    {
        try {
            $this->seatsioClient->charts->retrievePublishedVersion('unexistingChart');
            $this->fail();
        } catch (SeatsioException $e) {
            self::assertEquals('CHART_NOT_FOUND', $e->errors[0]->code);
            self::assertEquals('Chart not found: unexistingChart', $e->errors[0]->message);
            self::assertEquals('Chart not found: unexistingChart', $e->getMessage());
            self::assertNotNull($e->requestId);
        }
    }

}
