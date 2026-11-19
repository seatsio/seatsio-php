<?php

namespace Seatsio;

/**
 * Shared between a client and the clients derived from it (see SeatsioClient::withIdempotencyKey()).
 */
class RetrySettings
{
    public int $maxRetries;
    public bool $retryNonIdempotentRequests = false;

    public function __construct(int $maxRetries)
    {
        $this->maxRetries = $maxRetries;
    }
}
