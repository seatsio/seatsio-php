<?php

namespace Seatsio\Events;

use Seatsio\SeatsioClientTest;

class DeleteEventTest extends SeatsioClientTest
{

    /**
     * @expectedException \Seatsio\SeatsioException
     */
    public function test()
    {
        $chart = $this->seatsioClient->charts->create();
        $event = $this->seatsioClient->events->create($chart->key);

        $this->seatsioClient->events->delete($event->key);

        $this->seatsioClient->events->retrieve($event->key);
    }

}