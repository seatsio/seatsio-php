<?php

namespace Seatsio\Events;

use Seatsio\SeatsioClientTest;
use Seatsio\SeatsioException;

class DeleteEventTest extends SeatsioClientTest
{

    public function test()
    {
        $this->expectException(SeatsioException::class);

        $chart = $this->seatsioClient->charts->create();
        $event = $this->seatsioClient->events->create($chart->key);

        $this->seatsioClient->events->delete($event->key);

        $this->seatsioClient->events->retrieve($event->key);
    }

}
