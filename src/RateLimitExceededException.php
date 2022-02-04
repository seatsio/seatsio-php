<?php

namespace Seatsio;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class RateLimitExceededException extends SeatsioException
{
    public function __construct(RequestInterface $request, ResponseInterface $response)
    {
        parent::__construct($request, $response);
    }
}
