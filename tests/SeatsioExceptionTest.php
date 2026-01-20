<?php

namespace Seatsio;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;

class SeatsioExceptionTest extends SeatsioClientTest
{

    public function testCanInstantiateSeatsioExceptionWithoutRequestId()
    {
        $request = new Request(
            "GET",
            "http://dummy.uri"
        );
        $response = new Response(
            400,
            ["Content-Type" => "application/json"],
            "{\"errors\": [], \"messages\":[]}"
        );
        $exception = SeatsioException::from($request, $response);
        self::assertNull($exception->requestId);
        self::assertStringStartsWith("GET http://dummy.uri resulted in a `400 Bad Request` response.", $exception->getMessage());
    }

    public function testTimeoutThrowsSeatsioException()
    {
        $this->expectException(SeatsioException::class);
        $this->expectExceptionMessage("Connection timed out");

        $request = new Request("GET", "http://dummy.uri");
        $mock = new MockHandler([
            new ConnectException("Connection timed out", $request)
        ]);
        $handlerStack = HandlerStack::create($mock);
        $client = new Client([
            'handler' => $handlerStack,
            'timeout' => 0.01
        ]);

        $charts = new Charts\Charts($client);
        $charts->listAll();
    }
}
