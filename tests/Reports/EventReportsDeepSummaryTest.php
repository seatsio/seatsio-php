<?php

namespace Seatsio\Reports;

use Seatsio\Events\ObjectInfo;
use Seatsio\Events\ObjectProperties;
use Seatsio\SeatsioClientTest;

class EventReportsDeepSummaryTest extends SeatsioClientTest
{

    public function testDeepSummaryByStatus()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey);
        $this->seatsioClient->events->book($event->key, (new ObjectProperties("A-1")));

        $report = $this->seatsioClient->eventReports->deepSummaryByStatus($event->key);

        self::assertEquals($report[ObjectInfo::$BOOKED]["count"], 1);
        self::assertEquals($report[ObjectInfo::$BOOKED]["bySection"]["NO_SECTION"]["count"], 1);
        self::assertEquals($report[ObjectInfo::$BOOKED]["bySection"]["NO_SECTION"]["bySelectability"]["not_selectable"], 1);
    }

    public function testDeepSummaryByObjectType()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey);

        $report = $this->seatsioClient->eventReports->deepSummaryByObjectType($event->key);

        self::assertEquals($report["seat"]["count"], 32);
        self::assertEquals($report["seat"]["bySection"]["NO_SECTION"]["count"], 32);
        self::assertEquals($report["seat"]["bySection"]["NO_SECTION"]["bySelectability"]["selectable"], 32);
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
