<?php

namespace Seatsio;

class SeatsioTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var MockHttpClient
     */
    protected $httpClient;

    protected function setUp()
    {
        $this->httpClient = new MockHttpClient();
    }

    protected function assertHttpClientCalled($relativeUrl)
    {
        $this->assertTrue($this->httpClient->wasCalled($relativeUrl));
    }


}