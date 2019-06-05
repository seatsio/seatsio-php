<?php

namespace Seatsio\Reports;

use Seatsio\Events\ObjectProperties;
use Seatsio\Events\ObjectStatus;
use Seatsio\SeatsioClientTest;

class EventReportsSummaryTest extends SeatsioClientTest
{

    public function testSummaryByStatus()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey);
        $this->seatsioClient->events->book($event->key, (new ObjectProperties("A-1"))->setTicketType("ticketType1"), null, "order1");

        $report = $this->seatsioClient->eventReports->summaryByStatus($event->key);

        self::assertEquals(1, $report[ObjectStatus::$BOOKED]['count']);
        self::assertEquals(1, $report[ObjectStatus::$BOOKED]['bySection']['NO_SECTION']);
        self::assertEquals(1, $report[ObjectStatus::$BOOKED]['byCategoryKey']['9']);
        self::assertEquals(1, $report[ObjectStatus::$BOOKED]['byCategoryLabel']['Cat1']);

        self::assertEquals(231, $report[ObjectStatus::$FREE]['count']);
        self::assertEquals(231, $report[ObjectStatus::$FREE]['bySection']['NO_SECTION']);
        self::assertEquals(115, $report[ObjectStatus::$FREE]['byCategoryKey']['9']);
        self::assertEquals(116, $report[ObjectStatus::$FREE]['byCategoryKey']['10']);
        self::assertEquals(115, $report[ObjectStatus::$FREE]['byCategoryLabel']['Cat1']);
        self::assertEquals(116, $report[ObjectStatus::$FREE]['byCategoryLabel']['Cat2']);
    }

    public function testSummaryByCategoryKey()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey);
        $this->seatsioClient->events->book($event->key, (new ObjectProperties("A-1"))->setTicketType("ticketType1"), null, "order1");

        $report = $this->seatsioClient->eventReports->summaryByCategoryKey($event->key);

        self::assertEquals(116, $report['9']['count']);
        self::assertEquals(116, $report['9']['bySection']['NO_SECTION']);
        self::assertEquals(1, $report['9']['byStatus'][ObjectStatus::$BOOKED]);
        self::assertEquals(115, $report['9']['byStatus'][ObjectStatus::$FREE]);

        self::assertEquals(116, $report['10']['count']);
        self::assertEquals(116, $report['10']['bySection']['NO_SECTION']);
        self::assertEquals(116, $report['10']['byStatus'][ObjectStatus::$FREE]);
    }

    public function testSummaryByCategoryLabel()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey);
        $this->seatsioClient->events->book($event->key, (new ObjectProperties("A-1"))->setTicketType("ticketType1"), null, "order1");

        $report = $this->seatsioClient->eventReports->summaryByCategoryLabel($event->key);

        self::assertEquals(116, $report['Cat1']['count']);
        self::assertEquals(116, $report['Cat1']['bySection']['NO_SECTION']);
        self::assertEquals(1, $report['Cat1']['byStatus'][ObjectStatus::$BOOKED]);
        self::assertEquals(115, $report['Cat1']['byStatus'][ObjectStatus::$FREE]);

        self::assertEquals(116, $report['Cat2']['count']);
        self::assertEquals(116, $report['Cat2']['bySection']['NO_SECTION']);
        self::assertEquals(116, $report['Cat2']['byStatus'][ObjectStatus::$FREE]);
    }

    public function testSummaryBySection()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey);
        $this->seatsioClient->events->book($event->key, (new ObjectProperties("A-1"))->setTicketType("ticketType1"), null, "order1");

        $report = $this->seatsioClient->eventReports->summaryBySection($event->key);

        self::assertEquals(232, $report['NO_SECTION']['count']);
        self::assertEquals(1, $report['NO_SECTION']['byStatus'][ObjectStatus::$BOOKED]);
        self::assertEquals(231, $report['NO_SECTION']['byStatus'][ObjectStatus::$FREE]);
        self::assertEquals(116, $report['NO_SECTION']['byCategoryKey']['9']);
        self::assertEquals(116, $report['NO_SECTION']['byCategoryKey']['10']);
        self::assertEquals(116, $report['NO_SECTION']['byCategoryLabel']['Cat1']);
        self::assertEquals(116, $report['NO_SECTION']['byCategoryLabel']['Cat2']);
    }

}
