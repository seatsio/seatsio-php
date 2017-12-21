<?php

namespace Seatsio\Events;

use Seatsio\SeatsioClientTest;

class EventReportsTest extends SeatsioClientTest
{

    public function testReportItemProperties()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events()->create($chartKey);
        $this->seatsioClient->events()->book($event->key, (new Object("A-1"))->withTicketType("ticketType1"), null, "order1");

        $report = $this->seatsioClient->events()->reportByLabel($event->key);

        $reportItem = $report["A-1"][0];
        self::assertEquals("booked", $reportItem->status);
        self::assertEquals("A-1", $reportItem->label);
        self::assertEquals("uuid288", $reportItem->uuid);
        self::assertEquals("Cat1", $reportItem->categoryLabel);
        self::assertEquals(9, $reportItem->categoryKey);
        self::assertEquals("ticketType1", $reportItem->ticketType);
        self::assertEquals("order1", $reportItem->orderId);
        self::assertTrue($reportItem->forSale);
        self::assertNull($reportItem->section);
        self::assertNull($reportItem->entrance);
    }

    public function testReportItemPropertiesForGA()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events()->create($chartKey);
        $this->seatsioClient->events()->book($event->key, (new Object("GA1"))->withQuantity(5));

        $report = $this->seatsioClient->events()->reportByLabel($event->key);

        $reportItem = $report["GA1"][0];
        self::assertEquals(100, $reportItem->capacity);
        self::assertEquals(5, $reportItem->numBooked);
    }

    public function testByStatus()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events()->create($chartKey);
        $this->seatsioClient->events()->changeObjectStatus($event->key, "A-1", "lolzor");
        $this->seatsioClient->events()->changeObjectStatus($event->key, "A-2", "lolzor");
        $this->seatsioClient->events()->changeObjectStatus($event->key, "A-3", "booked");

        $report = $this->seatsioClient->events()->reportByStatus($event->key);
        self::assertCount(2, $report["lolzor"]);
        self::assertCount(1, $report["booked"]);
        self::assertCount(31, $report["free"]);
    }

    public function testByCategoryLabel()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events()->create($chartKey);

        $report = $this->seatsioClient->events()->reportByCategoryLabel($event->key);
        self::assertCount(17, $report["Cat1"]);
        self::assertCount(17, $report["Cat2"]);
    }

    public function testByCategoryKey()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events()->create($chartKey);

        $report = $this->seatsioClient->events()->reportByCategoryKey($event->key);
        self::assertCount(17, $report[9]);
        self::assertCount(17, $report[10]);
    }

    public function testByLabel()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events()->create($chartKey);

        $report = $this->seatsioClient->events()->reportByLabel($event->key);
        self::assertCount(1, $report["A-1"]);
        self::assertCount(1, $report["A-2"]);
    }

    public function testByUuid()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events()->create($chartKey);

        $report = $this->seatsioClient->events()->reportByUuid($event->key);
        self::assertNotNull($report["uuid300"]);
        self::assertNotNull($report["uuid301"]);
    }

    public function testByOrderId()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events()->create($chartKey);
        $this->seatsioClient->events()->book($event->key, "A-1", null, "order1");
        $this->seatsioClient->events()->book($event->key, "A-2", null, "order1");
        $this->seatsioClient->events()->book($event->key, "A-3", null, "order2");

        $report = $this->seatsioClient->events()->reportByOrderId($event->key);
        self::assertCount(2, $report["order1"]);
        self::assertCount(1, $report["order2"]);
        self::assertCount(31, $report["NO_ORDER_ID"]);
    }

    public function testBySection()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events()->create($chartKey);

        $report = $this->seatsioClient->events()->reportBySection($event->key);
        self::assertCount(34, $report["NO_SECTION"]);
    }

}