<?php

namespace Seatsio;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use RuntimeException;
use Seatsio\Http\IdempotencyKeyStatus;

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

    public ?string $idempotencyKey = null;

    public ?bool $idempotencyKeyHonored = null;

    public bool $replayed = false;

    private bool $outcomeIsUnknown = false;

    private bool $safeToRetryWithSameKey = false;

    public static function from(RequestInterface $request, ResponseInterface $response, bool $routeHonorsKeys = false, bool $anEarlierAttemptMayHaveRun = false): SeatsioException
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
        $status = IdempotencyKeyStatus::of($response);
        $exception->idempotencyKeyHonored = $status?->keyHonored();
        $exception->replayed = $status === IdempotencyKeyStatus::REPLAYED;
        $exception->outcomeIsUnknown = $code >= 500
            || self::hasIdempotencyKeyInUseError($code, $parsedResponse['errors'])
            || ($anEarlierAttemptMayHaveRun && !$exception->replayed);
        $exception->safeToRetryWithSameKey = $exception->outcomeIsUnknown
            && $exception->idempotencyKey !== null
            && ($exception->idempotencyKeyHonored ?? $routeHonorsKeys)
            && !$exception->replayed;
        return $exception;
    }

    public static function fromException(RequestInterface $request, \Throwable $exception): SeatsioException
    {
        return self::fromConnectionFailure($request, $exception, true, false);
    }

    public static function fromConnectionFailure(RequestInterface $request, \Throwable $exception, bool $outcomeIsUnknown, bool $safeToRetryWithSameKey): SeatsioException
    {
        $seatsioException = new SeatsioException($request, ['errors' => [], 'requestId' => null], $exception->getMessage(), $exception);
        $seatsioException->outcomeIsUnknown = $outcomeIsUnknown;
        $seatsioException->safeToRetryWithSameKey = $safeToRetryWithSameKey && $seatsioException->idempotencyKey !== null;
        return $seatsioException;
    }

    public function outcomeIsUnknown(): bool
    {
        return $this->outcomeIsUnknown;
    }

    public function isSafeToRetryWithSameKey(): bool
    {
        return $this->safeToRetryWithSameKey;
    }

    public static function isIdempotencyKeyInUse(ResponseInterface $response): bool
    {
        if ($response->getStatusCode() != 409) {
            return false;
        }
        return self::hasIdempotencyKeyInUseError(409, self::parseErrorResponse($response)['errors'] ?? []);
    }

    private static function hasIdempotencyKeyInUseError(int $statusCode, array $errors): bool
    {
        return $statusCode == 409 && ($errors[0]->code ?? null) === 'IDEMPOTENCY_KEY_IN_USE';
    }

    public function __construct(RequestInterface $request, array $parsedResponse, string $message, ?\Throwable $previous = null)
    {
        parent::__construct($message, 0, $previous);
        $this->errors = $parsedResponse['errors'];
        $this->requestId = $parsedResponse['requestId'];
        $this->idempotencyKey = $request->getHeaderLine(SeatsioClient::IDEMPOTENCY_KEY_HEADER) ?: null;
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

    private static function parseErrorResponse(ResponseInterface $response): ?array
    {
        if (!str_contains($response->getHeaderLine("content-type"), 'application/json')) {
            return null;
        }
        $body = (string)$response->getBody();
        $response->getBody()->rewind();
        $json = json_decode($body);
        if (!($json instanceof \stdClass) || !isset($json->errors) || !is_array($json->errors)) {
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
