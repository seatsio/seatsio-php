<?php

namespace Seatsio\Charts;

use Seatsio\Events\StatusChangeRequest;
use Seatsio\SeatsioClientTest;

class ListStatusChangesForObjectTest extends SeatsioClientTest
{

    public function test()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey);
        $this->seatsioClient->events->changeObjectStatusInBatch([
            (new StatusChangeRequest())->setEvent($event->key)->setObjects('A-1')->setStatus('s1'),
            (new StatusChangeRequest())->setEvent($event->key)->setObjects('A-1')->setStatus('s2'),
            (new StatusChangeRequest())->setEvent($event->key)->setObjects('A-2')->setStatus('s4'),
            (new StatusChangeRequest())->setEvent($event->key)->setObjects('A-1')->setStatus('s3'),
        ]);
        $this->waitForStatusChanges($event, 4);

        $statusChanges = $this->seatsioClient->events->statusChangesForObject($event->key, "A-1")->all();
        $statuses = array_map(function ($statusChange) {
            return $statusChange->status;
        }, iterator_to_array($statusChanges));

        self::assertEquals(["s3", "s2", "s1"], array_values($statuses));
    }

}
