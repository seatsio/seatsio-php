<?php

namespace Seatsio\EventLog;

use Seatsio\SeatsioClientTest;

class ListAllEventLogItemsTest extends SeatsioClientTest
{

    public function testListAll()
    {
        $chart = $this->seatsioClient->charts->create();
        $this->seatsioClient->charts->update($chart->key, "a chart");

        $this->waitUntilEventLogItemsAvailable();

        $eventLogItems = $this->seatsioClient->eventLog->listAll();
        $types = array_map(function ($eventLogItem) {
            return $eventLogItem->type;
        }, iterator_to_array($eventLogItems));

        self::assertEquals(["chart.created", "chart.published"], array_values($types));
    }

    public function testProperties()
    {
        $chart = $this->seatsioClient->charts->create();
        $this->seatsioClient->charts->update($chart->key, "a chart");

        $this->waitUntilEventLogItemsAvailable();

        $eventLogItem = $this->seatsioClient->eventLog->listAll()->current();

        self::assertEquals("chart.created", $eventLogItem->type);
        self::assertNotNull($eventLogItem->timestamp);
        self::assertGreaterThan(0, $eventLogItem->id);
        self::assertEquals((object)["key" => $chart->key, "workspaceKey" => $this->workspace->key], $eventLogItem->data);
    }

    private function waitUntilEventLogItemsAvailable()
    {
        sleep(2);
    }
}
