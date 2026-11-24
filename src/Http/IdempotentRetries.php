<?php

namespace Seatsio\Http;

use GuzzleHttp\Exception\TransferException;
use GuzzleHttp\Promise\Create;
use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\UriTemplate\UriTemplate;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Seatsio\SeatsioException;

class IdempotentRetries
{
    public const IDEMPOTENCY_KEY_HEADER = 'Idempotency-Key';
    public const PATH_PARAMS = 'path_params';

    private const SAFE_METHODS = ['GET', 'HEAD', 'OPTIONS', 'TRACE'];

    private SharedState $state;
    private ?string $idempotencyKey;

    public function __construct(SharedState $state, ?string $idempotencyKey)
    {
        $this->state = $state;
        $this->idempotencyKey = $idempotencyKey;
    }

    public function __invoke(callable $handler): callable
    {
        return function (RequestInterface $request, array $options) use ($handler): PromiseInterface {
            [$request, $pathTemplate] = self::expandPath($request, $options);
            if (self::isSafe($request)) {
                return $this->attempt($handler, $request, $options, null, 0);
            }
            $request = $this->withIdempotencyKey($request);
            $write = new Write($request, $request->getMethod() . ' ' . $pathTemplate, $this->state);
            return $this->attempt($handler, $request, $options, $write, 0);
        };
    }

    private static function expandPath(RequestInterface $request, array $options): array
    {
        $uri = $request->getUri();
        if (!isset($options[self::PATH_PARAMS])) {
            return [$request, $uri->getPath()];
        }
        $template = str_replace(['%7B', '%7D'], ['{', '}'], $uri->getPath());
        $path = UriTemplate::expand($template, $options[self::PATH_PARAMS]);
        return [$request->withUri($uri->withPath($path)), $template];
    }

    private function withIdempotencyKey(RequestInterface $request): RequestInterface
    {
        if ($request->hasHeader(self::IDEMPOTENCY_KEY_HEADER) || self::isMultipart($request)) {
            return $request;
        }
        return $request->withHeader(self::IDEMPOTENCY_KEY_HEADER, $this->idempotencyKey ?? self::randomUuid());
    }

    private function attempt(callable $handler, RequestInterface $request, array $options, ?Write $write, int $retryCount): PromiseInterface
    {
        return self::send($handler, $request, $options)->then(
            function (ResponseInterface $response) use ($handler, $request, $options, $write, $retryCount) {
                $write?->answered($response);
                if (self::isRetryable($response) && $this->waitBeforeRetry($retryCount, $write)) {
                    return $this->attempt($handler, $request, $options, $write, $retryCount + 1);
                }
                if ($response->getStatusCode() >= 400) {
                    throw SeatsioException::from($request, $response, $write?->routeHonorsKeys() ?? false);
                }
                return $response;
            },
            function ($reason) use ($handler, $request, $options, $write, $retryCount) {
                $failure = Create::exceptionFor($reason);
                if ($write === null) {
                    throw $failure instanceof TransferException ? SeatsioException::fromException($request, $failure) : $failure;
                }
                if ($write->failed($failure) && $this->waitBeforeRetry($retryCount, $write)) {
                    return $this->attempt($handler, $request, $options, $write, $retryCount + 1);
                }
                throw $write->connectionFailure($failure);
            }
        );
    }

    private static function send(callable $handler, RequestInterface $request, array $options): PromiseInterface
    {
        try {
            return $handler($request, $options);
        } catch (\Throwable $e) {
            return Create::rejectionFor($e);
        }
    }

    private static function isRetryable(ResponseInterface $response): bool
    {
        return $response->getStatusCode() == 429 || SeatsioException::isIdempotencyKeyInUse($response);
    }

    private function waitBeforeRetry(int $retryCount, ?Write $write): bool
    {
        if ($retryCount >= $this->state->maxRetries) {
            return false;
        }
        $delayMillis = 2 ** ($retryCount + 2) * 100;
        if ($write !== null && !$write->mayRetryAfter($delayMillis)) {
            return false;
        }
        $this->state->clock->sleep($delayMillis);
        return true;
    }

    private static function isSafe(RequestInterface $request): bool
    {
        return in_array(strtoupper($request->getMethod()), self::SAFE_METHODS, true);
    }

    private static function isMultipart(RequestInterface $request): bool
    {
        return str_starts_with(strtolower($request->getHeaderLine('Content-Type')), 'multipart/');
    }

    private static function randomUuid(): string
    {
        $bytes = random_bytes(16);
        $bytes[6] = chr((ord($bytes[6]) & 0x0f) | 0x40);
        $bytes[8] = chr((ord($bytes[8]) & 0x3f) | 0x80);
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($bytes), 4));
    }
}
