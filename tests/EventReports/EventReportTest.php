<?php

namespace Seatsio\EventReports;

use Seatsio\Events\ObjectProperties;
use Seatsio\Events\ObjectStatus;
use Seatsio\SeatsioClientTest;

class EventReportsTest extends SeatsioClientTest
{

    public function testReportItemProperties()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey);
        $extraData = ["foo" => "bar"];
        $this->seatsioClient->events->book($event->key, (new ObjectProperties("A-1"))->setTicketType("ticketType1")->setExtraData($extraData), null, "order1");

        $report = $this->seatsioClient->eventReports->byLabel($event->key);

        $reportItem = $report["A-1"][0];
        self::assertEquals(ObjectStatus::$BOOKED, $reportItem->status);
        self::assertEquals("A-1", $reportItem->label);
        self::assertEquals("Cat1", $reportItem->categoryLabel);
        self::assertEquals(9, $reportItem->categoryKey);
        self::assertEquals("ticketType1", $reportItem->ticketType);
        self::assertEquals("order1", $reportItem->orderId);
        self::assertEquals("seat", $reportItem->objectType);
        self::assertTrue($reportItem->forSale);
        self::assertNull($reportItem->section);
        self::assertNull($reportItem->entrance);
        self::assertEquals((object)$extraData, $reportItem->extraData);
    }

    public function testHoldToken()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey);
        $holdToken = $this->seatsioClient->holdTokens->create();
        $this->seatsioClient->events->hold($event->key, "A-1", $holdToken->holdToken);

        $report = $this->seatsioClient->eventReports->byLabel($event->key);

        $reportItem = $report["A-1"][0];
        self::assertEquals("$holdToken->holdToken", $reportItem->holdToken);
    }

    public function testReportItemPropertiesForGA()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey);
        $this->seatsioClient->events->book($event->key, (new ObjectProperties("GA1"))->setQuantity(5));

        $report = $this->seatsioClient->eventReports->byLabel($event->key);

        $reportItem = $report["GA1"][0];
        self::assertEquals(100, $reportItem->capacity);
        self::assertEquals(5, $reportItem->numBooked);
        self::assertEquals("generalAdmission", $reportItem->objectType);
    }

    public function testByStatus()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey);
        $this->seatsioClient->events->changeObjectStatus($event->key, "A-1", "lolzor");
        $this->seatsioClient->events->changeObjectStatus($event->key, "A-2", "lolzor");
        $this->seatsioClient->events->changeObjectStatus($event->key, "A-3", ObjectStatus::$BOOKED);

        $report = $this->seatsioClient->eventReports->byStatus($event->key);
        self::assertCount(2, $report["lolzor"]);
        self::assertCount(1, $report[ObjectStatus::$BOOKED]);
        self::assertCount(31, $report[ObjectStatus::$FREE]);
    }

    public function testBySpecificStatus()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey);
        $this->seatsioClient->events->changeObjectStatus($event->key, "A-1", "lolzor");
        $this->seatsioClient->events->changeObjectStatus($event->key, "A-2", "lolzor");
        $this->seatsioClient->events->changeObjectStatus($event->key, "A-3", ObjectStatus::$BOOKED);

        $report = $this->seatsioClient->eventReports->byStatus($event->key, "lolzor");
        self::assertCount(2, $report);
    }

    public function testByCategoryLabel()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey);

        $report = $this->seatsioClient->eventReports->byCategoryLabel($event->key);
        self::assertCount(17, $report["Cat1"]);
        self::assertCount(17, $report["Cat2"]);
    }

    public function testBySpecificCategoryLabel()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey);

        $report = $this->seatsioClient->eventReports->byCategoryLabel($event->key, "Cat1");
        self::assertCount(17, $report);
    }

    public function testByCategoryKey()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey);

        $report = $this->seatsioClient->eventReports->byCategoryKey($event->key);
        self::assertCount(17, $report[9]);
        self::assertCount(17, $report[10]);
    }

    public function testBySpecificCategoryKey()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey);

        $report = $this->seatsioClient->eventReports->byCategoryKey($event->key, 9);
        self::assertCount(17, $report);
    }

    public function testByLabel()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey);

        $report = $this->seatsioClient->eventReports->byLabel($event->key);
        self::assertCount(1, $report["A-1"]);
        self::assertCount(1, $report["A-2"]);
    }

    public function testBySpecificLabel()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey);

        $report = $this->seatsioClient->eventReports->byLabel($event->key, "A-1");
        self::assertCount(1, $report);
    }

    public function testByOrderId()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey);
        $this->seatsioClient->events->book($event->key, "A-1", null, "order1");
        $this->seatsioClient->events->book($event->key, "A-2", null, "order1");
        $this->seatsioClient->events->book($event->key, "A-3", null, "order2");

        $report = $this->seatsioClient->eventReports->byOrderId($event->key);
        self::assertCount(2, $report["order1"]);
        self::assertCount(1, $report["order2"]);
        self::assertCount(31, $report["NO_ORDER_ID"]);
    }

    public function testBySpecificOrderId()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey);
        $this->seatsioClient->events->book($event->key, "A-1", null, "order1");
        $this->seatsioClient->events->book($event->key, "A-2", null, "order1");
        $this->seatsioClient->events->book($event->key, "A-3", null, "order2");

        $report = $this->seatsioClient->eventReports->byOrderId($event->key, "order1");
        self::assertCount(2, $report);
    }

    public function testBySection()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey);

        $report = $this->seatsioClient->eventReports->bySection($event->key);
        self::assertCount(34, $report["NO_SECTION"]);
    }

    public function testBySpecificSection()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey);

        $report = $this->seatsioClient->eventReports->bySection($event->key, "NO_SECTION");
        self::assertCount(34, $report);
    }

}