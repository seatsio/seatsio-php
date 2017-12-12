<?php

namespace Seatsio;

use Unirest\Request;

class HttpClient
{


    public function get($relativeUrl)
    {
        // TODO Implement this
        $headers = array();
        $query = array();
        $response = Request::get('http://mockbin.com/request', $headers, $query);
        return $response;
    }
}