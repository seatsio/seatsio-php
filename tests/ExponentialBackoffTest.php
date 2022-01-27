<?php

namespace Seatsio;

class ExponentialBackoffTest extends SeatsioClientTest
{

    public function testAbortsEventuallyIfServerKeepsReturning429()
    {
        $start = time();
        try {
            $client = new SeatsioClient(Region::withUrl("https://httpbin.org"), "aSecretKey");
            $client->client->get("/status/429")->getBody();
            throw new \Exception("Should have failed");
        } catch (RateLimitExceededException $exception) {
            self::assertEquals($exception->getMessage(), "GET https://httpbin.org/status/429` resulted in a `429 TOO MANY REQUESTS` response.");
            $waitTime = time() - $start;
            self::assertGreaterThan(10, $waitTime);
            self::assertLessThan(20, $waitTime);
        }
    }

    public function testAbortsDirectlyIfServerReturnsOtherErrorThan429()
    {
        $start = time();
        try {
            $client = new SeatsioClient(Region::withUrl("https://httpbin.org"), "aSecretKey");
            $client->client->get("/status/400")->getBody();
            throw new \Exception("Should have failed");
        } catch (SeatsioException $exception) {
            self::assertEquals($exception->getMessage(), "GET https://httpbin.org/status/400` resulted in a `400 BAD REQUEST` response.");
            $waitTime = time() - $start;
            self::assertLessThan(2, $waitTime);
        }
    }

    public function testAbortsDirectlyIfServerReturns429ButMaxRetries0()
    {
        $start = time();
        try {
            $client = new SeatsioClient(Region::withUrl("https://httpbin.org"), "aSecretKey", null, 0);
            $client->client->get("/status/429")->getBody();
            throw new \Exception("Should have failed");
        } catch (SeatsioException $exception) {
            self::assertEquals($exception->getMessage(), "GET https://httpbin.org/status/429` resulted in a `429 TOO MANY REQUESTS` response.");
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
