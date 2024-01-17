<?php

namespace Seatsio\EventLog;

use Seatsio\Charts\ChartListParams;
use Seatsio\Events\CreateEventParams;
use Seatsio\Events\ForSaleConfig;
use Seatsio\SeatsioClientTest;
use function Functional\map;

class ListAllEventLogItemsTest extends SeatsioClientTest
{

    public function testListAll()
    {
        $chart = $this->seatsioClient->charts->create();
        $this->seatsioClient->charts->update($chart->key, 'a chart');

        $this->waitUntilEventLogItemsAvailable();

        $eventLogItems = $this->seatsioClient->eventLog->listAll();
        $types = map($eventLogItems, function ($eventLogItem) {
            return $eventLogItem->type;
        });

        self::assertEquals(['chart.created', 'chart.published'], array_values($types));
    }

    private function waitUntilEventLogItemsAvailable()
    {
        sleep(2);
    }
}
