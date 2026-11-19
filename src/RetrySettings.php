<?php

namespace Seatsio;

class RetrySettings
{
    public int $maxRetries;
    public bool $retryNonIdempotentRequests = false;

    public function __construct(int $maxRetries)
    {
        $this->maxRetries = $maxRetries;
    }
}
