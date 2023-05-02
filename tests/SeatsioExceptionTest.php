<?php

namespace Seatsio;

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
        $exception = new SeatsioException($request, $response);
        self::assertNull($exception->requestId);
        self::assertStringStartsWith("GET http://dummy.uri resulted in a `400 Bad Request` response.", $exception->getMessage());
    }
}
