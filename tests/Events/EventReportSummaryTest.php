<?php

namespace Seatsio\Events;

use Seatsio\SeatsioClientTest;

class EventReportsSummaryTest extends SeatsioClientTest
{

    public function testSummaryByStatus()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events()->create($chartKey);
        $this->seatsioClient->events()->book($event->key, (new ObjectProperties("A-1"))->setTicketType("ticketType1"), null, "order1");

        $report = $this->seatsioClient->events()->reports()->summaryByStatus($event->key);

        self::assertEquals(1, $report[ObjectStatus::$BOOKED]['count']);
        self::assertEquals(1, $report[ObjectStatus::$BOOKED]['bySection']['NO_SECTION']);
        self::assertEquals(1, $report[ObjectStatus::$BOOKED]['byCategoryKey']['9']);
        self::assertEquals(1, $report[ObjectStatus::$BOOKED]['byCategoryLabel']['Cat1']);

        self::assertEquals(33, $report[ObjectStatus::$FREE]['count']);
        self::assertEquals(33, $report[ObjectStatus::$FREE]['bySection']['NO_SECTION']);
        self::assertEquals(16, $report[ObjectStatus::$FREE]['byCategoryKey']['9']);
        self::assertEquals(17, $report[ObjectStatus::$FREE]['byCategoryKey']['10']);
        self::assertEquals(16, $report[ObjectStatus::$FREE]['byCategoryLabel']['Cat1']);
        self::assertEquals(17, $report[ObjectStatus::$FREE]['byCategoryLabel']['Cat2']);
    }

    public function testSummaryByCategoryKey()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events()->create($chartKey);
        $this->seatsioClient->events()->book($event->key, (new ObjectProperties("A-1"))->setTicketType("ticketType1"), null, "order1");

        $report = $this->seatsioClient->events()->reports()->summaryByCategoryKey($event->key);

        self::assertEquals(17, $report['9']['count']);
        self::assertEquals(17, $report['9']['bySection']['NO_SECTION']);
        self::assertEquals(1, $report['9']['byStatus'][ObjectStatus::$BOOKED]);
        self::assertEquals(16, $report['9']['byStatus'][ObjectStatus::$FREE]);

        self::assertEquals(17, $report['10']['count']);
        self::assertEquals(17, $report['10']['bySection']['NO_SECTION']);
        self::assertEquals(17, $report['10']['byStatus'][ObjectStatus::$FREE]);
    }

    public function testSummaryByCategoryLabel()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events()->create($chartKey);
        $this->seatsioClient->events()->book($event->key, (new ObjectProperties("A-1"))->setTicketType("ticketType1"), null, "order1");

        $report = $this->seatsioClient->events()->reports()->summaryByCategoryLabel($event->key);

        self::assertEquals(17, $report['Cat1']['count']);
        self::assertEquals(17, $report['Cat1']['bySection']['NO_SECTION']);
        self::assertEquals(1, $report['Cat1']['byStatus'][ObjectStatus::$BOOKED]);
        self::assertEquals(16, $report['Cat1']['byStatus'][ObjectStatus::$FREE]);

        self::assertEquals(17, $report['Cat2']['count']);
        self::assertEquals(17, $report['Cat2']['bySection']['NO_SECTION']);
        self::assertEquals(17, $report['Cat2']['byStatus'][ObjectStatus::$FREE]);
    }

    public function testSummaryBySection()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events()->create($chartKey);
        $this->seatsioClient->events()->book($event->key, (new ObjectProperties("A-1"))->setTicketType("ticketType1"), null, "order1");

        $report = $this->seatsioClient->events()->reports()->summaryBySection($event->key);

        self::assertEquals(34, $report['NO_SECTION']['count']);
        self::assertEquals(1, $report['NO_SECTION']['byStatus'][ObjectStatus::$BOOKED]);
        self::assertEquals(33, $report['NO_SECTION']['byStatus'][ObjectStatus::$FREE]);
        self::assertEquals(17, $report['NO_SECTION']['byCategoryKey']['9']);
        self::assertEquals(17, $report['NO_SECTION']['byCategoryKey']['10']);
        self::assertEquals(17, $report['NO_SECTION']['byCategoryLabel']['Cat1']);
        self::assertEquals(17, $report['NO_SECTION']['byCategoryLabel']['Cat2']);
    }

}