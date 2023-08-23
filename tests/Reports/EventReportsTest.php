<?php

namespace Seatsio\Reports;

use Seatsio\Common\IDs;
use Seatsio\Events\Channel;
use Seatsio\Events\CreateEventParams;
use Seatsio\Events\EventObjectInfo;
use Seatsio\Events\ObjectProperties;
use Seatsio\Events\TableBookingConfig;
use Seatsio\SeatsioClientTest;

class EventReportsTest extends SeatsioClientTest
{

    public function testReportItemProperties()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey);
        $extraData = ["foo" => "bar"];
        $this->seatsioClient->events->book($event->key, (new ObjectProperties("A-1"))->setTicketType("ticketType1")->setExtraData($extraData), null, "order1");
        $this->seatsioClient->events->channels->replace($event->key, [
            new Channel("channel1", "channel 1", "#FF0000", 1, ["A-1"])
        ]);

        $report = $this->seatsioClient->eventReports->byLabel($event->key);

        $reportItem = $report["A-1"][0];
        self::assertEquals(EventObjectInfo::$BOOKED, $reportItem->status);
        self::assertEquals("A-1", $reportItem->label);
        self::assertEquals(someLabels("1", "seat", "A", "row"), $reportItem->labels);
        self::assertEquals(new IDs("1", "A", null), $reportItem->ids);
        self::assertEquals("Cat1", $reportItem->categoryLabel);
        self::assertEquals(9, $reportItem->categoryKey);
        self::assertEquals("ticketType1", $reportItem->ticketType);
        self::assertEquals("order1", $reportItem->orderId);
        self::assertEquals("seat", $reportItem->objectType);
        self::assertTrue($reportItem->forSale);
        self::assertNull($reportItem->section);
        self::assertNull($reportItem->entrance);
        self::assertEquals((object)$extraData, $reportItem->extraData);
        self::assertFalse($reportItem->isAccessible);
        self::assertFalse($reportItem->hasRestrictedView);
        self::assertFalse($reportItem->isCompanionSeat);
        self::assertFalse($reportItem->isAvailable);
        self::assertEquals(EventObjectInfo::$BOOKED, $reportItem->availabilityReason);
        self::assertNull($reportItem->displayedObjectType);
        self::assertNull($reportItem->leftNeighbour);
        self::assertEquals("A-2", $reportItem->rightNeighbour);
        self::assertEquals("channel1", $reportItem->channel);
        self::assertNotNull($reportItem->distanceToFocalPoint);

        $gaItem = $report["GA1"][0];
        self::assertFalse($gaItem->variableOccupancy);
        self::assertEquals(1, $gaItem->minOccupancy);
        self::assertNull($gaItem->maxOccupancy);
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
        $holdToken = $this->seatsioClient->holdTokens->create();
        $this->seatsioClient->events->hold($event->key, (new ObjectProperties("GA1"))->setQuantity(3), $holdToken->holdToken);

        $report = $this->seatsioClient->eventReports->byLabel($event->key);

        $reportItem = $report["GA1"][0];
        self::assertEquals(100, $reportItem->capacity);
        self::assertEquals(5, $reportItem->numBooked);
        self::assertEquals(3, $reportItem->numHeld);
        self::assertEquals(92, $reportItem->numFree);
        self::assertEquals("generalAdmission", $reportItem->objectType);
        self::assertEquals(false, $reportItem->bookAsAWhole);
        self::assertNull($reportItem->isAccessible);
        self::assertNull($reportItem->hasRestrictedView);
        self::assertNull($reportItem->isCompanionSeat);
        self::assertNull($reportItem->displayedObjectType);
    }

    public function testReportItemPropertiesForTable()
    {
        $chartKey = $this->createTestChartWithTables();
        $event = $this->seatsioClient->events->create($chartKey, CreateEventParams::create()->setTableBookingConfig(TableBookingConfig::allByTable()));

        $report = $this->seatsioClient->eventReports->byLabel($event->key);

        $reportItem = $report["T1"][0];
        self::assertEquals(false, $reportItem->bookAsAWhole);
        self::assertEquals(6, $reportItem->numSeats);
    }

    public function testByStatus()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey);
        $this->seatsioClient->events->changeObjectStatus($event->key, "A-1", "lolzor");
        $this->seatsioClient->events->changeObjectStatus($event->key, "A-2", "lolzor");
        $this->seatsioClient->events->changeObjectStatus($event->key, "A-3", EventObjectInfo::$BOOKED);

        $report = $this->seatsioClient->eventReports->byStatus($event->key);
        self::assertCount(2, $report["lolzor"]);
        self::assertCount(1, $report[EventObjectInfo::$BOOKED]);
        self::assertCount(31, $report[EventObjectInfo::$FREE]);
    }

    public function testByStatus_emptyChart()
    {
        $chartKey = $this->seatsioClient->charts->create()->key;
        $event = $this->seatsioClient->events->create($chartKey);

        $report = $this->seatsioClient->eventReports->byStatus($event->key);
        self::assertEquals([], $report);
    }

    public function testBySpecificStatus()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey);
        $this->seatsioClient->events->changeObjectStatus($event->key, "A-1", "lolzor");
        $this->seatsioClient->events->changeObjectStatus($event->key, "A-2", "lolzor");
        $this->seatsioClient->events->changeObjectStatus($event->key, "A-3", EventObjectInfo::$BOOKED);

        $report = $this->seatsioClient->eventReports->byStatus($event->key, "lolzor");
        self::assertCount(2, $report);
    }

    public function testByObjectType()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey);

        $report = $this->seatsioClient->eventReports->byObjectType($event->key);
        self::assertCount(32, $report["seat"]);
        self::assertCount(2, $report["generalAdmission"]);
        self::assertCount(0, $report["table"]);
        self::assertCount(0, $report["booth"]);
    }

    public function testBySpecificObjectType()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey);

        $report = $this->seatsioClient->eventReports->byObjectType($event->key, "seat");
        self::assertCount(32, $report);
    }

    public function testBySpecificNonExistingStatus()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey);

        $report = $this->seatsioClient->eventReports->byStatus($event->key, "lolzor");
        self::assertEquals([], $report);
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

    public function testByAvailability()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey);
        $this->seatsioClient->events->book($event->key, "A-1");
        $this->seatsioClient->events->book($event->key, "A-2");

        $report = $this->seatsioClient->eventReports->byAvailability($event->key);
        self::assertCount(32, $report["available"]);
        self::assertCount(2, $report["not_available"]);
    }

    public function testByAvailabilityReason()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey);
        $this->seatsioClient->events->book($event->key, "A-1");
        $this->seatsioClient->events->book($event->key, "A-2");

        $report = $this->seatsioClient->eventReports->byAvailabilityReason($event->key);
        self::assertCount(32, $report["available"]);
        self::assertCount(2, $report["booked"]);
    }

    public function testBySpecificAvailability()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey);

        $report = $this->seatsioClient->eventReports->byAvailability($event->key, "available");
        self::assertCount(34, $report);
    }

    public function testBySpecificAvailabilityReason()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey);

        $report = $this->seatsioClient->eventReports->byAvailabilityReason($event->key, "available");
        self::assertCount(34, $report);
    }

    public function testByChannel()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey);
        $this->seatsioClient->events->channels->replace($event->key, [
            new Channel("channel1", "channel 1", "#FF0000", 1, ["A-1", "A-2"])
        ]);

        $report = $this->seatsioClient->eventReports->byChannel($event->key);

        self::assertCount(2, $report["channel1"]);
        self::assertCount(32, $report["NO_CHANNEL"]);
    }

    public function testBySpecificChannel()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey);
        $this->seatsioClient->events->channels->replace($event->key, [
            new Channel("channel1", "channel 1", "#FF0000", 1, ["A-1", "A-2"])
        ]);

        $report = $this->seatsioClient->eventReports->byChannel($event->key, 'channel1');

        self::assertCount(2, $report);
    }

}
