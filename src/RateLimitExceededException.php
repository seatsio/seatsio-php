<?php

namespace Seatsio;

use Psr\Http\Message\RequestInterface;

class RateLimitExceededException extends SeatsioException
{
    public function __construct(RequestInterface $request,array $parsedResponseInfo, string $message)
    {
        parent::__construct($request, $parsedResponseInfo, $message);
    }
}
