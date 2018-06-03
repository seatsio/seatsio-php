<?php

namespace Seatsio;

class ErrorHandlingTest extends \Seatsio\SeatsioClientTest
{

    /**
     * @expectedException \Seatsio\SeatsioException
     */
    public function test4xx()
    {
        $this->seatsioClient->charts->retrievePublishedVersion('unexistingChart');
    }

}