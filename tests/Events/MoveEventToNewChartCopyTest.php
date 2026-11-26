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

    public function testEventsAreMovedToOneNewChartCopy()
    {
        $chart = $this->seatsioClient->charts->create();
        $event1 = $this->seatsioClient->events->create($chart->key);
        $event2 = $this->seatsioClient->events->create($chart->key);

        $movedEvents = $this->seatsioClient->events->moveEventsToNewChartCopy([$event2->key, $event1->key]);

        self::assertEquals([$event2->key, $event1->key], array_map(fn($e) => $e->key, $movedEvents));
        self::assertNotEquals($chart->key, $movedEvents[0]->chartKey);
        self::assertEquals($movedEvents[0]->chartKey, $movedEvents[1]->chartKey);
    }
}
