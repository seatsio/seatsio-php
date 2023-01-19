<?php

namespace Seatsio;

class ExponentialBackoffTest extends SeatsioClientTest
{

    public function testAbortsEventuallyIfServerKeepsReturning429()
    {
        $start = time();
        try {
            $client = new SeatsioClient(Region::withUrl("https://httpbin.seatsio.net"), "aSecretKey");
            $client->client->get("/status/429")->getBody();
            throw new \Exception("Should have failed");
        } catch (RateLimitExceededException $exception) {
            self::assertEquals("GET https://httpbin.seatsio.net/status/429 resulted in a `429 Too Many Requests` response. Body: ", $exception->getMessage());
            $waitTime = time() - $start;
            self::assertGreaterThan(10, $waitTime);
            self::assertLessThan(20, $waitTime);
        }
    }

    public function testAbortsDirectlyIfServerReturnsOtherErrorThan429()
    {
        $start = time();
        try {
            $client = new SeatsioClient(Region::withUrl("https://httpbin.seatsio.net"), "aSecretKey");
            $client->client->get("/status/400")->getBody();
            throw new \Exception("Should have failed");
        } catch (SeatsioException $exception) {
            self::assertEquals("GET https://httpbin.seatsio.net/status/400 resulted in a `400 Bad Request` response. Body: ", $exception->getMessage());
            $waitTime = time() - $start;
            self::assertLessThan(2, $waitTime);
        }
    }

    public function testAbortsDirectlyIfServerReturns429ButMaxRetries0()
    {
        $start = time();
        try {
            $client = new SeatsioClient(Region::withUrl("https://httpbin.seatsio.net"), "aSecretKey", null, 0);
            $client->client->get("/status/429")->getBody();
            throw new \Exception("Should have failed");
        } catch (SeatsioException $exception) {
            self::assertEquals("GET https://httpbin.seatsio.net/status/429 resulted in a `429 Too Many Requests` response. Body: ", $exception->getMessage());
            $waitTime = time() - $start;
            self::assertLessThan(2, $waitTime);
        }
    }

    public function testReturnsSuccessfullyWhenTheServerSendsA429FirstAndThenASuccessfulResponse()
    {
        $client = new SeatsioClient(Region::withUrl("https://httpbin.seatsio.net"), "aSecretKey");
        for ($i = 0; $i < 20; ++$i) {
            $res = $client->client->get("/status/429:0.25,204:0.75")->getBody();
            self::assertEquals('', $res);
        }
    }

}
