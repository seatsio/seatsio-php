<?php

namespace Seatsio\Events;


use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Seatsio\SeatsioException;

class BestAvailableObjectsNotFoundException extends SeatsioException
{
    public function __construct(RequestInterface $request, ResponseInterface $response, array $parsedResponseInfo, string $message)
    {
        parent::__construct($request, $response, $parsedResponseInfo, $message);
    }
}
