<?php

namespace Seatsio;

use Psr\Http\Message\RequestInterface;

class RateLimitExceededException extends SeatsioException
{
    public function __construct(RequestInterface $request, array $parsedResponse, string $message)
    {
        parent::__construct($request, $parsedResponse, $message);
    }
}
