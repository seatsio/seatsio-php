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
     * The Idempotency-Key sent with a POST, PUT or DELETE request, or null for other requests.
     *
     * @var string|null
     */
    public $idempotencyKey;

    private bool $outcomeIsUnknown = false;

    public static function from(RequestInterface $request, ResponseInterface $response): SeatsioException
    {
        $code = $response->getStatusCode();
        $parsedResponse = self::extractInfo($response);
        $message = self::message($request, $response, $parsedResponse['messages']);
        if ($code == 429) {
            $exception = new RateLimitExceededException($request, $parsedResponse, $message);
        } else if (self::isBestAvailableObjectsNotFound($parsedResponse['errors'])) {
            $exception = new BestAvailableObjectsNotFoundException($request, $parsedResponse, $message);
        } else {
            $exception = new SeatsioException($request, $parsedResponse, $message);
        }
        $exception->outcomeIsUnknown = $code >= 500 || self::isIdempotencyKeyInUse($response);
        return $exception;
    }

    public static function fromException($request, \Throwable $exception)
    {
        $seatsioException = new SeatsioException($request, ['errors' => [], 'requestId' => null], $exception->getMessage());
        $seatsioException->outcomeIsUnknown = true;
        return $seatsioException;
    }

    /**
     * True if the request may or may not have been executed: the connection failed, the server answered with a 5xx,
     * or the key was still in use after all retries (the first request may still succeed).
     * Only then can retrying the same operation (with the same idempotencyKey) give a different result.
     */
    public function outcomeIsUnknown(): bool
    {
        return $this->outcomeIsUnknown;
    }

    public static function isIdempotencyKeyInUse(ResponseInterface $response): bool
    {
        if ($response->getStatusCode() != 409) {
            return false;
        }
        $errors = self::parseErrorResponse($response)['errors'] ?? null;
        return $errors !== null && ($errors[0]->code ?? null) === 'IDEMPOTENCY_KEY_IN_USE';
    }

    public function __construct(RequestInterface $request, array $parsedResponse, string $message)
    {
        parent::__construct($message);
        $this->errors = $parsedResponse['errors'];
        $this->requestId = $parsedResponse['requestId'];
        $this->idempotencyKey = $request->getHeaderLine('Idempotency-Key') ?: null;
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
        $json = self::parseErrorResponse($response);
        if ($json === null) {
            return ["messages" => [], "errors" => [], "requestId" => null];
        }
        $mapper = SeatsioJsonMapper::create();
        $errors = $mapper->mapArray($json['errors'], array(), 'Seatsio\ApiError');
        return ["messages" => $json['messages'] ?? [], "errors" => $errors, "requestId" => $json['requestId'] ?? null];
    }

    /**
     * Returns the decoded body (with objects for the errors), or null if the response is not a JSON error response
     * with at least one error.
     */
    private static function parseErrorResponse(ResponseInterface $response): ?array
    {
        if (!str_contains($response->getHeaderLine("content-type"), 'application/json')) {
            return null;
        }
        $body = (string)$response->getBody();
        $response->getBody()->rewind();
        $json = json_decode($body);
        if (!($json instanceof \stdClass) || !isset($json->errors) || !is_array($json->errors) || empty($json->errors)) {
            return null;
        }
        foreach ($json->errors as $error) {
            if (!($error instanceof \stdClass)) {
                return null;
            }
        }
        $messages = isset($json->messages) && is_array($json->messages) ? $json->messages : [];
        $requestId = isset($json->requestId) && is_string($json->requestId) ? $json->requestId : null;
        return ['errors' => $json->errors, 'messages' => $messages, 'requestId' => $requestId];
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
