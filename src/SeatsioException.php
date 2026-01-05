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
        $parsedResponse = self::extractInfo($response);
        $message = self::message($request, $response, $parsedResponse['messages']);
        if ($code == 429) {
            return new RateLimitExceededException($request, $parsedResponse, $message);
        } else if (self::isBestAvailableObjectsNotFound($parsedResponse['errors'])) {
            throw new BestAvailableObjectsNotFoundException($request, $parsedResponse, $message);
        }
        return new SeatsioException($request, $parsedResponse, $message);
    }

    public static function fromException($request, \Exception $exception)
    {
        throw new SeatsioException($request, ['errors' => [], 'requestId' => null], $exception->getMessage());
    }

    public function __construct(RequestInterface $request, array $parsedResponse, string $message)
    {
        parent::__construct($message);
        $this->errors = $parsedResponse['errors'];
        $this->requestId = $parsedResponse['requestId'];
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

    private static function extractInfo($response): array
    {
        $contentType = $response->getHeaderLine("content-type");
        if (str_contains($contentType, 'application/json')) {
            $json = GuzzleResponseDecoder::decodeToObject($response);
            $mapper = SeatsioJsonMapper::create();
            $errors = $mapper->mapArray($json->errors, array(), 'Seatsio\ApiError');
            return ["messages" => $json->messages, "errors" => $errors, "requestId" => $json->requestId ?? null];
        }
        return ["messages" => [], "errors" => [], "requestId" => null];
    }

    private static function isBestAvailableObjectsNotFound($errors): bool
    {
        if (!is_array($errors) || empty($errors)) {
            return false;
        }
        foreach ($errors as $error) {
            if (is_object($error) && property_exists($error, 'code')) {
                if ($error->code === 'BEST_AVAILABLE_OBJECTS_NOT_FOUND') {
                    return true;
                }
            }
        }
        return false;
    }
}
