<?php

namespace Seatsio;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;

class SeatsioExceptionTest extends SeatsioClientTest
{

    public function testCanInstantiateSeatsioExceptionWithoutRequestId()
    {
        $request = new DummyRequest();
        $response = new DummyResponse(
            "application/json",
            "{\"errors\": [], \"messages\":[]}"
        );
        $exception = new SeatsioException($request, $response);
        self::assertNull($exception->requestId);
    }
}


class DummyRequest implements RequestInterface
{

    public function __construct()
    {
    }

    public function getProtocolVersion()
    {
    }

    public function withProtocolVersion($version)
    {
    }

    public function getHeaders()
    {
    }

    public function hasHeader($name)
    {
    }

    public function getHeader($name)
    {
    }

    public function getHeaderLine($name)
    {
    }

    public function withHeader($name, $value)
    {
    }

    public function withAddedHeader($name, $value)
    {
    }

    public function withoutHeader($name)
    {
    }

    public function getBody()
    {
    }

    public function withBody(StreamInterface $body)
    {
    }

    public function getRequestTarget()
    {
    }

    public function withRequestTarget($requestTarget)
    {
    }

    public function getMethod()
    {
    }

    public function withMethod($method)
    {
    }

    public function getUri()
    {
    }

    public function withUri(UriInterface $uri, $preserveHost = false)
    {
    }
}

class DummyResponse implements ResponseInterface
{


    private $contentType;
    private $body;

    public function __construct(string $contentType, string $body)
    {
        $this->contentType = $contentType;
        $this->body = $body;
    }

    public function getProtocolVersion()
    {
    }

    public function withProtocolVersion($version)
    {
    }

    public function getHeaders()
    {
    }

    public function hasHeader($name)
    {
    }

    public function getHeader($name)
    {
    }

    public function getHeaderLine($name)
    {
        if ($name === "content-type") {
            return $this->contentType;
        }
        return null;
    }

    public function withHeader($name, $value)
    {
    }

    public function withAddedHeader($name, $value)
    {
    }

    public function withoutHeader($name)
    {
    }

    public function getBody()
    {
        return $this->body;
    }

    public function withBody(StreamInterface $body)
    {
    }

    public function getStatusCode()
    {
    }

    public function withStatus($code, $reasonPhrase = '')
    {
    }

    public function getReasonPhrase()
    {
    }
}
