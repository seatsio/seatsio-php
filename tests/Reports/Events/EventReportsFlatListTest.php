<?php

namespace Reports\Events;

use Seatsio\Events\EventObjectInfo;
use Seatsio\Seasons\SeasonCreationParams;
use Seatsio\SeatsioClientTest;
use RuntimeException;

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

    public function testFlatListWithSeasonBookingsNotPropagated()
    {
        $chartKey = $this->createTestChart();
        $season = $this->seatsioClient->seasons->create($chartKey, (new SeasonCreationParams())->setNumberOfEvents(1));
        $event = $season->events[0];
        $this->seatsioClient->events->book($season->key, ["A-1", "A-2"]);
        $this->seatsioClient->events->book($event->key, ["A-3"]);

        $reportWithPropagation = $this->seatsioClient->eventReports->flatList($season->key);
        $reportWithoutPropagation = $this->seatsioClient->eventReports->withSeasonBookingsNotPropagated()->flatList($season->key);

        self::assertEquals(EventObjectInfo::$BOOKED, self::findByLabel($reportWithPropagation, "A-3")->status);
        self::assertNotEquals(EventObjectInfo::$BOOKED, self::findByLabel($reportWithoutPropagation, "A-3")->status);
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

    private static function findByLabel(array $report, string $label): EventObjectInfo
    {
        foreach ($report as $item) {
            if ($item->label === $label) {
                return $item;
            }
        }
        throw new RuntimeException("no item found with label $label");
    }

}

