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

    /**
     * @param $request RequestInterface
     * @param $response ResponseInterface
     */
    public function __construct($request, $response)
    {
        $info = self::extractInfo($response);
        parent::__construct(self::message($request, $response, $info['messages']));
        $this->errors = $info['errors'];
        $this->requestId = $info['requestId'];
    }

    /**
     * @param $request RequestInterface
     * @param $response ResponseInterface
     * @return string
     */
    private static function message($request, $response, $messages)
    {
        $message = sprintf(
            '%s %s` resulted in a `%s %s` response.',
            $request->getMethod(),
            $request->getUri(),
            $response->getStatusCode(),
            $response->getReasonPhrase()
        );
        if ($messages) {
            $message .= ' Reason: ' . implode(', ', $messages);
        }
        return $message;
    }

    /**
     * @param $response ResponseInterface
     * @return array
     */
    private static function extractInfo($response)
    {
        $contentType = $response->getHeaderLine("content-type");
        if (strpos($contentType, 'application/json') !== false) {
            $json = \GuzzleHttp\json_decode($response->getBody());
            $mapper = SeatsioJsonMapper::create();
            $errors = $mapper->mapArray($json->errors, array(), 'Seatsio\ApiError');
            return ["messages" => $json->messages, "errors" => $errors, "requestId" => $json->requestId];
        }
        return ["messages" => [], "errors" => [], "requestId" => null];
    }
}
