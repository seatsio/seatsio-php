<?php

namespace Seatsio\Events;

use Seatsio\SeatsioClientTest;

class MoveEventToNewChartCopyTest extends SeatsioClientTest
{
    public function testEventIsMovedToNewChartCopy()
    {
        $chart = $this->seatsioClient->charts->create();
        $event = $this->seatsioClient->events->create($chart->key);

        $movedEvent = $this->seatsioClient->events->moveEventToNewChartCopy($event->key);

        self::assertNotEquals($event->chartKey, $movedEvent->chartKey);
        self::assertEquals($event->key, $movedEvent->key);
    }
}
