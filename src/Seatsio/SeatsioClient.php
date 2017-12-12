<?php

namespace Seatsio;

class SeatsioClient
{

    public function __construct($httpClient)
    {
        $this->charts = new Resource($httpClient);
    }

}