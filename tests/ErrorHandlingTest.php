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
            self::assertStringStartsWith('Chart not found: unexistingChart was not found in workspace', $e->errors[0]->message);
            self::assertStringStartsWith('Chart not found: unexistingChart was not found in workspace', $e->getMessage());
            self::assertNotNull($e->requestId);
        }
    }

    public function test5xx()
    {
        try {
            $client = new SeatsioClient(Region::withUrl("https://httpbin.seatsio.net"), "aSecretKey");
            $client->client->get("/status/500")->getBody();
            throw new \Exception("Should have failed");
        } catch (SeatsioException $e) {
            self::assertStringContainsString('500 Internal Server Error', $e->getMessage());
        }
    }

    public function testInvalidHost()
    {
        try {
            $client = new SeatsioClient(Region::withUrl("https://this-is-a-url-that-should-not-exist.com"), "aSecretKey");
            $client->client->get("/")->getBody();
            throw new \Exception("Should have failed");
        } catch (SeatsioException $e) {
            self::assertStringContainsString('Could not resolve host: this-is-a-url-that-should-not-exist.com', $e->getMessage());
        }
    }

}
