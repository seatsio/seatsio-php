<?php

namespace Seatsio;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class RateLimitExceededException extends SeatsioException
{
    /**
     * @param $request RequestInterface
     * @param $response ResponseInterface
     */
    public function __construct($request, $response)
    {
        parent::__construct($request, $response);
    }
}
