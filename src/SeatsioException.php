<?php

namespace Seatsio;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use RuntimeException;

class SeatsioException extends RuntimeException
{

    /**
     * @var ApiError[]
     */
    public $errors;

    /**
     * @var string
     */
    public $requestId;

    public static function from(RequestInterface $request, ResponseInterface $response): SeatsioException
    {
        $code = $response->getStatusCode();
        if ($code == 429) {
            return new RateLimitExceededException($request, $response);
        }
        return new SeatsioException($request, $response);
    }

    public function __construct(RequestInterface $request, ResponseInterface $response)
    {
        $info = self::extractInfo($response);
        $requestId = $info['requestId'];
        parent::__construct(self::message($request, $response, $info['messages']));
        $this->errors = $info['errors'];
        $this->requestId = $requestId;
    }

    private static function message($request, $response, $messages)
    {

        if ($messages) {
            return implode(', ', $messages);
        } else {
            return sprintf(
                '%s %s resulted in a `%s %s` response. Body: %s',
                $request->getMethod(),
                $request->getUri(),
                $response->getStatusCode(),
                $response->getReasonPhrase(),
                $response->getBody()
            );
        }
    }

    private static function extractInfo($response)
    {
        $contentType = $response->getHeaderLine("content-type");
        if (strpos($contentType, 'application/json') !== false) {
            $json = GuzzleResponseDecoder::decodeToObject($response);
            $mapper = SeatsioJsonMapper::create();
            $errors = $mapper->mapArray($json->errors, array(), 'Seatsio\ApiError');
            return ["messages" => $json->messages, "errors" => $errors, "requestId" => $json->requestId ?? null];
        }
        return ["messages" => [], "errors" => [], "requestId" => null];
    }
}
