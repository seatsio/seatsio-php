<?php

namespace Seatsio\Http;

class SharedState
{
    public int $maxRetries;
    public RetryClock $clock;

    private array $routesHonoringKeys = [];

    public function __construct(int $maxRetries, RetryClock $clock)
    {
        $this->maxRetries = $maxRetries;
        $this->clock = $clock;
    }

    public function remember(string $route, ?IdempotencyKeyStatus $status): void
    {
        if ($status !== null) {
            $this->routesHonoringKeys[$route] = $status->keyHonored();
        }
    }

    public function honorsKeys(string $route): bool
    {
        return $this->routesHonoringKeys[$route] ?? false;
    }
}
