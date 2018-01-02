<?php

namespace Seatsio\Charts;

use Seatsio\Events\ObjectProperties;
use Seatsio\SeatsioClientTest;

class ListStatusChangesTest extends SeatsioClientTest
{

    public function test()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events()->create($chartKey);
        $this->seatsioClient->events()->book($event->key, "A-1");
        $this->seatsioClient->events()->book($event->key, "A-2");
        $this->seatsioClient->events()->book($event->key, "A-3");

        $statusChanges = $this->seatsioClient->events()->statusChanges($event->key)->all();
        $objectIds = \Functional\map($statusChanges, function($statusChange) { return $statusChange->objectLabelOrUuid; });

        self::assertEquals(["A-3", "A-2", "A-1"], array_values($objectIds));
    }

    public function testPropertiesOfStatusChange()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events()->create($chartKey);
        $object = (new ObjectProperties("A-1"))->setExtraData(["foo" => "bar"]);
        $this->seatsioClient->events()->book($event->key, $object, null, "orderId");

        $statusChanges = $this->seatsioClient->events()->statusChanges($event->key)->all();
        $statusChange = $statusChanges->current();

        self::assertNotNull($statusChange->id);
        self::assertNotNull($statusChange->date);
        self::assertEquals("orderId", $statusChange->orderId);
        self::assertEquals("A-1", $statusChange->objectLabelOrUuid);
        self::assertEquals(ObjectStatus::$BOOKED, $statusChange->status);
        self::assertEquals($event->id, $statusChange->eventId);
        self::assertEquals((object)["foo" => "bar"], $statusChange->extraData);
    }

}