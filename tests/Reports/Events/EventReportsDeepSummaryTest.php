<?php

namespace Reports\Events;

use Seatsio\Events\EventObjectInfo;
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

        self::assertEquals($report[EventObjectInfo::$BOOKED]["count"], 1);
        self::assertEquals($report[EventObjectInfo::$BOOKED]["bySection"]["NO_SECTION"]["count"], 1);
        self::assertEquals($report[EventObjectInfo::$BOOKED]["bySection"]["NO_SECTION"]["byAvailability"]["not_available"], 1);
    }

    public function testDeepSummaryByObjectType()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey);

        $report = $this->seatsioClient->eventReports->deepSummaryByObjectType($event->key);

        self::assertEquals($report["seat"]["count"], 32);
        self::assertEquals($report["seat"]["bySection"]["NO_SECTION"]["count"], 32);
        self::assertEquals($report["seat"]["bySection"]["NO_SECTION"]["byAvailability"]["available"], 32);
    }

    public function testDeepSummaryByCategoryKey()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey);
        $this->seatsioClient->events->book($event->key, (new ObjectProperties("A-1")));

        $report = $this->seatsioClient->eventReports->deepSummaryByCategoryKey($event->key);

        self::assertEquals($report["9"]["count"], 116);
        self::assertEquals($report["9"]["bySection"]["NO_SECTION"]["count"], 116);
        self::assertEquals($report["9"]["bySection"]["NO_SECTION"]["byAvailability"]["not_available"], 1);
    }

    public function testDeepSummaryByCategoryLabel()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey);
        $this->seatsioClient->events->book($event->key, (new ObjectProperties("A-1")));

        $report = $this->seatsioClient->eventReports->deepSummaryByCategoryLabel($event->key);

        self::assertEquals($report["Cat1"]["count"], 116);
        self::assertEquals($report["Cat1"]["bySection"]["NO_SECTION"]["count"], 116);
        self::assertEquals($report["Cat1"]["bySection"]["NO_SECTION"]["byAvailability"]["not_available"], 1);
    }

    public function testDeepSummaryBySection()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey);
        $this->seatsioClient->events->book($event->key, (new ObjectProperties("A-1")));

        $report = $this->seatsioClient->eventReports->deepSummaryBySection($event->key);

        self::assertEquals($report["NO_SECTION"]["count"], 232);
        self::assertEquals($report["NO_SECTION"]["byCategoryLabel"]["Cat1"]["count"], 116);
        self::assertEquals($report["NO_SECTION"]["byCategoryLabel"]["Cat1"]["byAvailability"]["not_available"], 1);
    }

    public function testDeepSummaryByZone()
    {
        $chartKey = $this->createTestChartWithZones();
        $event = $this->seatsioClient->events->create($chartKey);

        $report = $this->seatsioClient->eventReports->deepSummaryByZone($event->key);

        self::assertEquals($report["midtrack"]["count"], 6032);
        self::assertEquals($report["midtrack"]["byCategoryLabel"]["Mid Track Stand"]["count"], 6032);
    }

    public function testDeepSummaryByAvailability()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey);
        $this->seatsioClient->events->book($event->key, (new ObjectProperties("A-1")));

        $report = $this->seatsioClient->eventReports->deepSummaryByAvailability($event->key);

        self::assertEquals($report["not_available"]["count"], 1);
        self::assertEquals($report["not_available"]["byCategoryLabel"]["Cat1"]["count"], 1);
        self::assertEquals($report["not_available"]["byCategoryLabel"]["Cat1"]["bySection"]["NO_SECTION"], 1);
    }

    public function testDeepSummaryByAvailabilityReason()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey);
        $this->seatsioClient->events->book($event->key, (new ObjectProperties("A-1")));

        $report = $this->seatsioClient->eventReports->deepSummaryByAvailabilityReason($event->key);

        self::assertEquals($report["booked"]["count"], 1);
        self::assertEquals($report["booked"]["byCategoryLabel"]["Cat1"]["count"], 1);
        self::assertEquals($report["booked"]["byCategoryLabel"]["Cat1"]["bySection"]["NO_SECTION"], 1);
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
