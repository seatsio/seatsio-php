<?php

namespace Seatsio\Charts;

use Seatsio\Events\Event;
use Seatsio\Events\EventObjectInfo;
use Seatsio\Events\ObjectProperties;
use Seatsio\Events\StatusChangeRequest;
use Seatsio\Events\TableBookingConfig;
use Seatsio\SeatsioClientTest;
use function Functional\map;

class ListStatusChangesTest extends SeatsioClientTest
{

    public function test()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey);
        $this->seatsioClient->events->changeObjectStatusInBatch([
            new StatusChangeRequest($event->key, 'A-1', EventObjectInfo::$BOOKED),
            new StatusChangeRequest($event->key, 'A-2', EventObjectInfo::$BOOKED),
            new StatusChangeRequest($event->key, 'A-3', EventObjectInfo::$BOOKED)
        ]);
        $this->waitForStatusChanges($event);

        $statusChanges = $this->seatsioClient->events->statusChanges($event->key)->all();
        $objectIds = map($statusChanges, function ($statusChange) {
            return $statusChange->objectLabel;
        });

        self::assertEquals(["A-3", "A-2", "A-1"], array_values($objectIds));
    }

    public function testPropertiesOfStatusChange()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey);
        $object = (new ObjectProperties("A-1"))->setExtraData(["foo" => "bar"]);
        $this->seatsioClient->events->book($event->key, $object, null, "orderId");
        $this->waitForStatusChanges($event);

        $statusChanges = $this->seatsioClient->events->statusChanges($event->key)->all();
        $statusChange = $statusChanges->current();

        self::assertNotNull($statusChange->id);
        self::assertNotNull($statusChange->date);
        self::assertEquals("orderId", $statusChange->orderId);
        self::assertEquals("A-1", $statusChange->objectLabel);
        self::assertEquals(EventObjectInfo::$BOOKED, $statusChange->status);
        self::assertEquals($event->id, $statusChange->eventId);
        self::assertEquals((object)["foo" => "bar"], $statusChange->extraData);
        self::assertEquals("API_CALL", $statusChange->origin->type);
        self::assertNotNull($statusChange->origin->ip);
        self::assertTrue($statusChange->isPresentOnChart);
        self::assertNull($statusChange->notPresentOnChartReason);
    }

    public function testNotPresentOnChartAnymore()
    {
        $chartKey = $this->createTestChartWithTables();
        $event = $this->seatsioClient->events->create($chartKey, null, TableBookingConfig::allByTable());
        $this->seatsioClient->events->book($event->key, "T1");
        $this->seatsioClient->events->update($event->key, null, null, TableBookingConfig::allBySeat());
        $this->waitForStatusChanges($event);

        $statusChanges = $this->seatsioClient->events->statusChanges($event->key)->all();
        $statusChange = $statusChanges->current();

        self::assertFalse($statusChange->isPresentOnChart);
        self::assertEquals("SWITCHED_TO_BOOK_BY_SEAT", $statusChange->notPresentOnChartReason);
    }

    public function testFilter()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey);
        $this->seatsioClient->events->changeObjectStatusInBatch([
            new StatusChangeRequest($event->key, 'A-1', EventObjectInfo::$BOOKED),
            new StatusChangeRequest($event->key, 'A-2', EventObjectInfo::$BOOKED),
            new StatusChangeRequest($event->key, 'B-1', EventObjectInfo::$BOOKED),
            new StatusChangeRequest($event->key, 'A-3', EventObjectInfo::$BOOKED)
        ]);
        $this->waitForStatusChanges($event);

        $statusChanges = $this->seatsioClient->events->statusChanges($event->key, "A-")->all();
        $objectIds = map($statusChanges, function ($statusChange) {
            return $statusChange->objectLabel;
        });

        self::assertEquals(["A-3", "A-2", "A-1"], array_values($objectIds));
    }

    public function testSortAsc()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey);
        $this->seatsioClient->events->changeObjectStatusInBatch([
            new StatusChangeRequest($event->key, 'A-1', EventObjectInfo::$BOOKED),
            new StatusChangeRequest($event->key, 'A-2', EventObjectInfo::$BOOKED),
            new StatusChangeRequest($event->key, 'B-1', EventObjectInfo::$BOOKED),
            new StatusChangeRequest($event->key, 'A-3', EventObjectInfo::$BOOKED)
        ]);
        $this->waitForStatusChanges($event);

        $statusChanges = $this->seatsioClient->events->statusChanges($event->key, null, "objectLabel")->all();
        $objectIds = map($statusChanges, function ($statusChange) {
            return $statusChange->objectLabel;
        });

        self::assertEquals(["A-1", "A-2", "A-3", "B-1"], array_values($objectIds));
    }

    public function testSortAscPageBefore()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey);
        $this->seatsioClient->events->changeObjectStatusInBatch([
            new StatusChangeRequest($event->key, 'A-1', EventObjectInfo::$BOOKED),
            new StatusChangeRequest($event->key, 'A-2', EventObjectInfo::$BOOKED),
            new StatusChangeRequest($event->key, 'B-1', EventObjectInfo::$BOOKED),
            new StatusChangeRequest($event->key, 'A-3', EventObjectInfo::$BOOKED)
        ]);
        $this->waitForStatusChanges($event);

        $statusChangeLister = $this->seatsioClient->events->statusChanges($event->key, null, "objectLabel");
        $allStatusChanges = iterator_to_array($statusChangeLister->all(), false);
        $b1ID = $allStatusChanges[2]->id;
        $statusChanges = $statusChangeLister->pageBefore($b1ID, 2)->items;
        $objectIds = map($statusChanges, function ($statusChange) {
            return $statusChange->objectLabel;
        });

        self::assertEquals(["A-1", "A-2"], array_values($objectIds));
    }

    public function testSortAscPageAfter()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey);
        $this->seatsioClient->events->changeObjectStatusInBatch([
            new StatusChangeRequest($event->key, 'A-1', EventObjectInfo::$BOOKED),
            new StatusChangeRequest($event->key, 'A-2', EventObjectInfo::$BOOKED),
            new StatusChangeRequest($event->key, 'B-1', EventObjectInfo::$BOOKED),
            new StatusChangeRequest($event->key, 'A-3', EventObjectInfo::$BOOKED)
        ]);
        $this->waitForStatusChanges($event);

        $statusChangeLister = $this->seatsioClient->events->statusChanges($event->key, null, "objectLabel");
        $allStatusChanges = iterator_to_array($statusChangeLister->all(), false);
        $a1ID = $allStatusChanges[0]->id;
        $statusChanges = $statusChangeLister->pageAfter($a1ID, 2)->items;
        $objectIds = map($statusChanges, function ($statusChange) {
            return $statusChange->objectLabel;
        });

        self::assertEquals(["A-2", "A-3"], array_values($objectIds));
    }

    public function testSortDesc()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey);
        $this->seatsioClient->events->changeObjectStatusInBatch([
            new StatusChangeRequest($event->key, 'A-1', EventObjectInfo::$BOOKED),
            new StatusChangeRequest($event->key, 'A-2', EventObjectInfo::$BOOKED),
            new StatusChangeRequest($event->key, 'B-1', EventObjectInfo::$BOOKED),
            new StatusChangeRequest($event->key, 'A-3', EventObjectInfo::$BOOKED)
        ]);
        $this->waitForStatusChanges($event);

        $statusChanges = $this->seatsioClient->events->statusChanges($event->key, null, "objectLabel", "DESC")->all();
        $objectIds = map($statusChanges, function ($statusChange) {
            return $statusChange->objectLabel;
        });

        self::assertEquals(["B-1", "A-3", "A-2", "A-1"], array_values($objectIds));
    }
}
