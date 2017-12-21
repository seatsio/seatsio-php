<?php

namespace Seatsio\Charts;

use Seatsio\Events\Object;
use Seatsio\SeatsioClientTest;

class ListStatusChangesForObjectTest extends SeatsioClientTest
{

    public function test()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events()->create($chartKey);
        $this->seatsioClient->events()->changeObjectStatus($event->key, "A-1", "s1");
        $this->seatsioClient->events()->changeObjectStatus($event->key, "A-1", "s2");
        $this->seatsioClient->events()->changeObjectStatus($event->key, "A-2", "s4");
        $this->seatsioClient->events()->changeObjectStatus($event->key, "A-1", "s3");

        $statusChanges = $this->seatsioClient->events()->statusChanges($event->key, "A-1")->all();
        $statuses = \Functional\map($statusChanges, function($statusChange) { return $statusChange->status; });

        self::assertEquals(["s3", "s2", "s1"], array_values($statuses));
    }

}