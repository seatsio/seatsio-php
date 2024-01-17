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
        $this->seatsioClient->charts->update($chart->key, "a chart");

        $this->waitUntilEventLogItemsAvailable();

        $eventLogItems = $this->seatsioClient->eventLog->listAll();
        $types = map($eventLogItems, function ($eventLogItem) {
            return $eventLogItem->type;
        });

        self::assertEquals(["chart.created", "chart.published"], array_values($types));
    }

    public function testProperties()
    {
        $chart = $this->seatsioClient->charts->create();
        $this->seatsioClient->charts->update($chart->key, "a chart");

        $this->waitUntilEventLogItemsAvailable();

        $eventLogItem = $this->seatsioClient->eventLog->listAll()->current();

        self::assertEquals("chart.created", $eventLogItem->type);
        self::assertEquals($this->workspace->key, $eventLogItem->workspaceKey);
        self::assertNotNull($eventLogItem->date);
        self::assertGreaterThan(0, $eventLogItem->id);
        self::assertEquals((object)["key" => $chart->key], $eventLogItem->data);
    }

    private function waitUntilEventLogItemsAvailable()
    {
        sleep(2);
    }
}
