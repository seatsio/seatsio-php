<?php

namespace Seatsio\Charts;

use Seatsio\Events\ObjectProperties;
use Seatsio\Events\ObjectStatus;
use Seatsio\SeatsioClientTest;
use function Functional\map;

class ListStatusChangesTest extends SeatsioClientTest
{

    public function test()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey);
        $this->seatsioClient->events->book($event->key, "A-1");
        $this->seatsioClient->events->book($event->key, "A-2");
        $this->seatsioClient->events->book($event->key, "A-3");

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

        $statusChanges = $this->seatsioClient->events->statusChanges($event->key)->all();
        $statusChange = $statusChanges->current();

        self::assertNotNull($statusChange->id);
        self::assertNotNull($statusChange->date);
        self::assertEquals("orderId", $statusChange->orderId);
        self::assertEquals("A-1", $statusChange->objectLabel);
        self::assertEquals(ObjectStatus::$BOOKED, $statusChange->status);
        self::assertEquals($event->id, $statusChange->eventId);
        self::assertEquals((object)["foo" => "bar"], $statusChange->extraData);
        self::assertEquals("API_CALL", $statusChange->origin->type);
        self::assertNotNull($statusChange->origin->ip);
    }

    public function testFilter()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey);
        $this->seatsioClient->events->book($event->key, "A-1");
        $this->seatsioClient->events->book($event->key, "A-2");
        $this->seatsioClient->events->book($event->key, "B-1");
        $this->seatsioClient->events->book($event->key, "A-3");

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
        $this->seatsioClient->events->book($event->key, "A-1");
        $this->seatsioClient->events->book($event->key, "A-2");
        $this->seatsioClient->events->book($event->key, "B-1");
        $this->seatsioClient->events->book($event->key, "A-3");

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
        $this->seatsioClient->events->book($event->key, "A-1");
        $this->seatsioClient->events->book($event->key, "A-2");
        $this->seatsioClient->events->book($event->key, "B-1");
        $this->seatsioClient->events->book($event->key, "A-3");

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
        $this->seatsioClient->events->book($event->key, "A-1");
        $this->seatsioClient->events->book($event->key, "A-2");
        $this->seatsioClient->events->book($event->key, "B-1");
        $this->seatsioClient->events->book($event->key, "A-3");

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
        $this->seatsioClient->events->book($event->key, "A-1");
        $this->seatsioClient->events->book($event->key, "A-2");
        $this->seatsioClient->events->book($event->key, "B-1");
        $this->seatsioClient->events->book($event->key, "A-3");

        $statusChanges = $this->seatsioClient->events->statusChanges($event->key, null, "objectLabel", "DESC")->all();
        $objectIds = map($statusChanges, function ($statusChange) {
            return $statusChange->objectLabel;
        });

        self::assertEquals(["B-1", "A-3", "A-2", "A-1"], array_values($objectIds));
    }
}
