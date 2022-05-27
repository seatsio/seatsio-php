<?php

namespace Seatsio\Charts;

use Seatsio\Events\StatusChangeRequest;
use Seatsio\SeatsioClientTest;
use function Functional\map;

class ListStatusChangesForObjectTest extends SeatsioClientTest
{

    public function test()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey);
        $this->seatsioClient->events->changeObjectStatusInBatch([
            new StatusChangeRequest($event->key, 'A-1', 's1'),
            new StatusChangeRequest($event->key, 'A-1', 's2'),
            new StatusChangeRequest($event->key, 'A-2', 's4'),
            new StatusChangeRequest($event->key, 'A-1', 's3')
        ]);
        $this->waitForStatusChanges($event);

        $statusChanges = $this->seatsioClient->events->statusChangesForObject($event->key, "A-1")->all();
        $statuses = map($statusChanges, function ($statusChange) {
            return $statusChange->status;
        });

        self::assertEquals(["s3", "s2", "s1"], array_values($statuses));
    }

}
