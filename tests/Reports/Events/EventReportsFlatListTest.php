<?php

namespace Reports\Events;

use Seatsio\Events\EventObjectInfo;
use Seatsio\SeatsioClientTest;

class EventReportsFlatListTest extends SeatsioClientTest
{

    public function testFlatList()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey);
        $this->seatsioClient->events->book($event->key, "A-1");

        $report = $this->seatsioClient->eventReports->flatList($event->key);

        self::assertEquals("A-1", $report[0]->label);
        self::assertEquals(EventObjectInfo::$BOOKED, $report[0]->status);
    }

    public function testFlatListIsSortedByLabel()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey);

        $report = $this->seatsioClient->eventReports->flatList($event->key);

        $labels = array_map(fn($item) => $item->label, $report);
        $sorted = $labels;
        sort($sorted);
        self::assertEquals($sorted, $labels);
    }

    public function testFlatListCsv()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey);

        $csv = $this->seatsioClient->eventReports->flatListCsv($event->key);

        self::assertStringContainsString("A-1", $csv);
        self::assertStringContainsString("A-2", $csv);
    }

}

