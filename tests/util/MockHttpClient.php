<?php

namespace Seatsio;

class MockHttpClient
{

    private $routes;

    public function __construct()
    {
        $this->routes = array();
        $this->calledRoutes = array();
    }


    public function get($relativeUrl)
    {
        $this->calledRoutes[$relativeUrl] += 1;
    }

    public function registerResponseOnGet($relativeUrl, $responseCode, $body)
    {
        $this->routes[$relativeUrl] = new MockRoute($relativeUrl, $responseCode, $body);
    }

    public function wasCalled($relativeUrl)
    {
        return $this->calledRoutes[$relativeUrl] >= 1;
    }

}

class MockRoute
{

    private $relativeUrl;
    private $responseCode;
    private $body;

    public function __construct($relativeUrl, $responseCode, $body)
    {
        $this->relativeUrl = $relativeUrl;
        $this->responseCode = $responseCode;
        $this->body = $body;
    }


}