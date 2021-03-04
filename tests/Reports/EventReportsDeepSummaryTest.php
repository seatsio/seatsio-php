<?php

namespace Seatsio\Reports;

use Seatsio\Events\Channel;
use Seatsio\Events\ObjectProperties;
use Seatsio\Events\ObjectStatus;
use Seatsio\SeatsioClientTest;

class EventReportsDeepSummaryTest extends SeatsioClientTest
{

    public function testDeepSummaryByStatus()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey);
        $this->seatsioClient->events->book($event->key, (new ObjectProperties("A-1")));

        $report = $this->seatsioClient->eventReports->deepSummaryByStatus($event->key);

        self::assertEquals($report[ObjectStatus::$BOOKED]["count"], 1);
        self::assertEquals($report[ObjectStatus::$BOOKED]["bySection"]["NO_SECTION"]["count"], 1);
        self::assertEquals($report[ObjectStatus::$BOOKED]["bySection"]["NO_SECTION"]["bySelectability"]["not_selectable"], 1);
    }

    public function testDeepSummaryByCategoryKey()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey);
        $this->seatsioClient->events->book($event->key, (new ObjectProperties("A-1")));

        $report = $this->seatsioClient->eventReports->deepSummaryByCategoryKey($event->key);

        self::assertEquals($report["9"]["count"], 116);
        self::assertEquals($report["9"]["bySection"]["NO_SECTION"]["count"], 116);
        self::assertEquals($report["9"]["bySection"]["NO_SECTION"]["bySelectability"]["not_selectable"], 1);
    }

    public function testDeepSummaryByCategoryLabel()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey);
        $this->seatsioClient->events->book($event->key, (new ObjectProperties("A-1")));

        $report = $this->seatsioClient->eventReports->deepSummaryByCategoryLabel($event->key);

        self::assertEquals($report["Cat1"]["count"], 116);
        self::assertEquals($report["Cat1"]["bySection"]["NO_SECTION"]["count"], 116);
        self::assertEquals($report["Cat1"]["bySection"]["NO_SECTION"]["bySelectability"]["not_selectable"], 1);
    }

    public function testDeepSummaryBySection()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey);
        $this->seatsioClient->events->book($event->key, (new ObjectProperties("A-1")));

        $report = $this->seatsioClient->eventReports->deepSummaryBySection($event->key);

        self::assertEquals($report["NO_SECTION"]["count"], 232);
        self::assertEquals($report["NO_SECTION"]["byCategoryLabel"]["Cat1"]["count"], 116);
        self::assertEquals($report["NO_SECTION"]["byCategoryLabel"]["Cat1"]["bySelectability"]["not_selectable"], 1);
    }

    public function testDeepSummaryBySelectability()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey);
        $this->seatsioClient->events->book($event->key, (new ObjectProperties("A-1")));

        $report = $this->seatsioClient->eventReports->deepSummaryBySelectability($event->key);

        self::assertEquals($report["not_selectable"]["count"], 1);
        self::assertEquals($report["not_selectable"]["byCategoryLabel"]["Cat1"]["count"], 1);
        self::assertEquals($report["not_selectable"]["byCategoryLabel"]["Cat1"]["bySection"]["NO_SECTION"], 1);
    }

    public function testDeepSummaryByChannel()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey);
        $this->seatsioClient->events->book($event->key, (new ObjectProperties("A-1")));

        $report = $this->seatsioClient->eventReports->deepSummaryByChannel($event->key);

        self::assertEquals($report["NO_CHANNEL"]["count"], 232);
        self::assertEquals($report["NO_CHANNEL"]["byCategoryLabel"]["Cat1"]["count"], 116);
        self::assertEquals($report["NO_CHANNEL"]["byCategoryLabel"]["Cat1"]["bySection"]["NO_SECTION"], 116);
    }
}
