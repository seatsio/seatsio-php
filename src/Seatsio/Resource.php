<?php

namespace Seatsio;

class Resource
{

    public function __construct($httpClient)
    {
        $this->httpClient = $httpClient;
    }

    public function all()
    {
        return $this->httpClient->get("/charts");
    }

}