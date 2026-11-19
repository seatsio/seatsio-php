<?php

namespace Seatsio;

class RetrySettings
{
    public int $maxRetries;
    private ?bool $retryNonIdempotentRequests = null;
    private ?RetrySettings $inheritedFrom;

    public function __construct(int $maxRetries, ?RetrySettings $inheritedFrom = null)
    {
        $this->maxRetries = $maxRetries;
        $this->inheritedFrom = $inheritedFrom;
    }

    public function inherit(): RetrySettings
    {
        return new RetrySettings($this->maxRetries, $this);
    }

    public function retryNonIdempotentRequests(): bool
    {
        return $this->retryNonIdempotentRequests ?? $this->inheritedFrom?->retryNonIdempotentRequests() ?? false;
    }

    public function setRetryNonIdempotentRequests(bool $retryNonIdempotentRequests): void
    {
        $this->retryNonIdempotentRequests = $retryNonIdempotentRequests;
    }
}
