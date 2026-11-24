<?php

namespace Seatsio\Http;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Seatsio\SeatsioException;

class Write
{
    public const RETRY_WINDOW_MILLIS = 5 * 60 * 1000;

    private RequestInterface $request;
    private string $route;
    private SharedState $state;
    private int $start;
    private bool $mayHaveReachedTheServer = false;
    private bool $mayHaveRun = false;

    public function __construct(RequestInterface $request, string $route, SharedState $state)
    {
        $this->request = $request;
        $this->route = $route;
        $this->state = $state;
        $this->start = $state->clock->millis();
    }

    public function answered(ResponseInterface $response): void
    {
        $this->mayHaveReachedTheServer = true;
        if (SeatsioException::isIdempotencyKeyInUse($response)) {
            $this->mayHaveRun = true;
        }
        $this->state->remember($this->route, IdempotencyKeyStatus::of($response));
    }

    public function failed(\Throwable $failure): bool
    {
        $delivery = Delivery::of($failure);
        if ($delivery === Delivery::NEVER_SENT) {
            return true;
        }
        if ($delivery === Delivery::NOT_RETRYABLE) {
            return false;
        }
        $this->mayHaveReachedTheServer = true;
        $this->mayHaveRun = true;
        return $delivery === Delivery::RESPONSE_LOST && $this->routeHonorsKeys();
    }

    public function mayRetryAfter(int $delayMillis): bool
    {
        return !$this->mayHaveReachedTheServer || $this->startsWithinRetryWindow($delayMillis);
    }

    public function mayHaveRun(): bool
    {
        return $this->mayHaveRun;
    }

    public function routeHonorsKeys(): bool
    {
        return $this->request->getHeaderLine(IdempotentRetries::IDEMPOTENCY_KEY_HEADER) !== ''
            && $this->state->honorsKeys($this->route);
    }

    public function connectionFailure(\Throwable $failure): SeatsioException
    {
        $safeToRetryWithSameKey = !$this->mayHaveRun || ($this->routeHonorsKeys() && $this->startsWithinRetryWindow(0));
        return SeatsioException::fromConnectionFailure($this->request, $failure, $this->mayHaveRun, $safeToRetryWithSameKey);
    }

    private function startsWithinRetryWindow(int $delayMillis): bool
    {
        return $this->state->clock->millis() + $delayMillis - $this->start <= self::RETRY_WINDOW_MILLIS;
    }
}
