<?php

namespace Seatsio;

class ExponentialBackoffTest extends SeatsioClientTest
{

    public function testAbortsEventuallyIfServerKeepsReturning429()
    {
        $start = time();
        try {
            $client = new SeatsioClient(Region::withUrl("https://mockbin.org"), "aSecretKey");
            $client->client->get("/bin/0381d6f4-0155-4b8c-937b-73d3d88b2a3f")->getBody();
            throw new \Exception("Should have failed");
        } catch (RateLimitExceededException $exception) {
            self::assertEquals("GET https://mockbin.org/bin/0381d6f4-0155-4b8c-937b-73d3d88b2a3f resulted in a `429 Too Many Requests` response. Request ID: 123456.", $exception->getMessage());
            $waitTime = time() - $start;
            self::assertGreaterThan(10, $waitTime);
            self::assertLessThan(20, $waitTime);
        }
    }

    public function testAbortsDirectlyIfServerReturnsOtherErrorThan429()
    {
        $start = time();
        try {
            $client = new SeatsioClient(Region::withUrl("https://mockbin.org"), "aSecretKey");
            $client->client->get("/bin/1eea3aab-2bb2-4f92-99c2-50d942fb6294")->getBody();
            throw new \Exception("Should have failed");
        } catch (SeatsioException $exception) {
            self::assertEquals($exception->getMessage(), "GET https://mockbin.org/bin/1eea3aab-2bb2-4f92-99c2-50d942fb6294 resulted in a `400 Bad Request` response. Request ID: .");
            $waitTime = time() - $start;
            self::assertLessThan(2, $waitTime);
        }
    }

    public function testAbortsDirectlyIfServerReturns429ButMaxRetries0()
    {
        $start = time();
        try {
            $client = new SeatsioClient(Region::withUrl("https://mockbin.org"), "aSecretKey", null, 0);
            $client->client->get("/bin/0381d6f4-0155-4b8c-937b-73d3d88b2a3f")->getBody();
            throw new \Exception("Should have failed");
        } catch (SeatsioException $exception) {
            self::assertEquals($exception->getMessage(), "GET https://mockbin.org/bin/0381d6f4-0155-4b8c-937b-73d3d88b2a3f resulted in a `429 Too Many Requests` response. Request ID: 123456.");
            $waitTime = time() - $start;
            self::assertLessThan(2, $waitTime);
        }
    }

    public function testReturnsSuccessfullyWhenTheServerSendsA429FirstAndThenASuccessfulResponse()
    {
        $client = new SeatsioClient(Region::withUrl("https://httpbin.org"), "aSecretKey");
        for ($i = 0; $i < 20; ++$i) {
            $res = $client->client->get("/status/429:0.25,204:0.75")->getBody();
            self::assertEquals('', $res);
        }
    }

}
