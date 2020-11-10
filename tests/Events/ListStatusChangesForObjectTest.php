<?php

namespace Seatsio\Charts;

use Seatsio\SeatsioClientTest;
use function Functional\map;

class ListStatusChangesForObjectTest extends SeatsioClientTest
{

    public function test()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey);
        $this->seatsioClient->events->changeObjectStatus($event->key, "A-1", "s1");
        $this->seatsioClient->events->changeObjectStatus($event->key, "A-1", "s2");
        $this->seatsioClient->events->changeObjectStatus($event->key, "A-2", "s4");
        $this->seatsioClient->events->changeObjectStatus($event->key, "A-1", "s3");

        $statusChanges = $this->seatsioClient->events->statusChangesForObject($event->key, "A-1")->all();
        $statuses = map($statusChanges, function($statusChange) { return $statusChange->status; });

        self::assertEquals(["s3", "s2", "s1"], array_values($statuses));
    }

}
