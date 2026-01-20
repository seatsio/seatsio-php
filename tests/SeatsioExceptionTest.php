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

        $request = new Request("GET", "http://dummy.uri/charts");
        $connectException = new ConnectException("Connection timed out", $request);
        throw SeatsioException::fromException($request, $connectException);
    }
}
