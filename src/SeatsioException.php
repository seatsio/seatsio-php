<?php

namespace Seatsio;

use GuzzleHttp\Utils;
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

    /**
     * @param $request RequestInterface
     * @param $response ResponseInterface
     */
    public function __construct($request, $response)
    {
        $info = self::extractInfo($response);
        $requestId = $info['requestId'];
        parent::__construct(self::message($request, $response, $info['messages'], $requestId));
        $this->errors = $info['errors'];
        $this->requestId = $requestId;
    }

    private static function message($request, $response, $messages, $requestId)
    {
        $message = sprintf(
            '%s %s resulted in a `%s %s` response. Request ID: %s.',
            $request->getMethod(),
            $request->getUri(),
            $response->getStatusCode(),
            $response->getReasonPhrase(),
            $requestId
        );
        if ($messages) {
            $message .= ' Reason: ' . implode(', ', $messages);
        }
        return $message;
    }

    private static function extractInfo($response)
    {
        $contentType = $response->getHeaderLine("content-type");
        if (strpos($contentType, 'application/json') !== false) {
            $json = Utils::jsonDecode($response->getBody());
            $mapper = SeatsioJsonMapper::create();
            $errors = $mapper->mapArray($json->errors, array(), 'Seatsio\ApiError');
            return ["messages" => $json->messages, "errors" => $errors, "requestId" => $json->requestId ?? null];
        }
        return ["messages" => [], "errors" => [], "requestId" => null];
    }
}
