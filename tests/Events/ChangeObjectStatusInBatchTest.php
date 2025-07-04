<?php

namespace Seatsio\Events;

use Seatsio\Seasons\SeasonCreationParams;
use Seatsio\SeatsioClientTest;
use Seatsio\SeatsioException;

class ChangeObjectStatusInBatchTest extends SeatsioClientTest
{

    public function test()
    {
        $chartKey1 = $this->createTestChart();
        $chartKey2 = $this->createTestChart();
        $event1 = $this->seatsioClient->events->create($chartKey1);
        $event2 = $this->seatsioClient->events->create($chartKey2);

        $response = $this->seatsioClient->events->changeObjectStatusInBatch([
            (new StatusChangeRequest())->setEvent($event1->key)->setObjects("A-1")->setStatus("lolzor"),
            (new StatusChangeRequest())->setEvent($event2->key)->setObjects("A-2")->setStatus("lolzor")
        ]);

        self::assertEquals('lolzor', $response[0]->objects['A-1']->status);
        $objectInfo1 = $this->seatsioClient->events->retrieveObjectInfo($event1->key, "A-1");
        self::assertEquals("lolzor", $objectInfo1->status);

        self::assertEquals('lolzor', $response[1]->objects['A-2']->status);
        $objectInfo2 = $this->seatsioClient->events->retrieveObjectInfo($event2->key, "A-2");
        self::assertEquals("lolzor", $objectInfo2->status);
    }

    public function testCanPassInExtraData()
    {
        $chartKey1 = $this->createTestChart();
        $event1 = $this->seatsioClient->events->create($chartKey1);

        $response = $this->seatsioClient->events->changeObjectStatusInBatch([
            (new StatusChangeRequest())->setEvent($event1->key)->setObjects((new ObjectProperties("A-1"))->setExtraData(["foo" => "bar"]))->setStatus("lolzor"),
        ]);

        self::assertEquals('lolzor', $response[0]->objects['A-1']->status);
        $objectInfo1 = $this->seatsioClient->events->retrieveObjectInfo($event1->key, "A-1");
        self::assertEquals("bar", $objectInfo1->extraData->foo);
    }

    public function testChannelKeys()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey, (new CreateEventParams())->setChannels([
            new Channel("channelKey1", "channel 1", "#FF0000", 1, ["A-1"])
        ]));

        $response = $this->seatsioClient->events->changeObjectStatusInBatch([
            (new StatusChangeRequest())->setEvent($event->key)->setObjects("A-1")->setStatus("lolzor")->setChannelKeys(["channelKey1"])
        ]);

        self::assertEquals('lolzor', $response[0]->objects['A-1']->status);
    }

    public function testIgnoreChannels()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey, (new CreateEventParams())->setChannels([
            new Channel("channelKey1", "channel 1", "#FF0000", 1, ["A-1"])
        ]));

        $response = $this->seatsioClient->events->changeObjectStatusInBatch([
            (new StatusChangeRequest())->setEvent($event->key)->setObjects("A-1")->setStatus("lolzor")->setIgnoreChannels(true)
        ]);

        self::assertEquals('lolzor', $response[0]->objects['A-1']->status);
    }

    public function testAllowedPreviousStatuses()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey);

        try {
            $this->seatsioClient->events->changeObjectStatusInBatch([
                (new StatusChangeRequest())
                    ->setEvent($event->key)
                    ->setStatus("lolzor")
                    ->setObjects("A-1")
                    ->setAllowedPreviousStatuses(['someOtherStatus'])
            ]);
            throw new \Exception("Should have failed");
        } catch (SeatsioException $exception) {
            self::assertEquals(1, sizeof($exception->errors));
            self::assertEquals("ILLEGAL_STATUS_CHANGE", $exception->errors[0]->code);
        }
    }

    public function testRejectedPreviousStatuses()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey);

        try {
            $this->seatsioClient->events->changeObjectStatusInBatch([
                (new StatusChangeRequest())
                    ->setEvent($event->key)
                    ->setStatus("lolzor")
                    ->setObjects("A-1")
                    ->setRejectedPreviousStatuses(['free'])
            ]);
            throw new \Exception("Should have failed");
        } catch (SeatsioException $exception) {
            self::assertEquals(1, sizeof($exception->errors));
            self::assertEquals("ILLEGAL_STATUS_CHANGE", $exception->errors[0]->code);
        }
    }

    public function testRelease()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey);
        $this->seatsioClient->events->book($event->key, "A-1");

        $response = $this->seatsioClient->events->changeObjectStatusInBatch([
            (new StatusChangeRequest())->setType(StatusChangeRequest::$TYPE_RELEASE)->setEvent($event->key)->setObjects("A-1"),
        ]);

        self::assertEquals(EventObjectInfo::$FREE, $response[0]->objects['A-1']->status);
        $objectInfo1 = $this->seatsioClient->events->retrieveObjectInfo($event->key, "A-1");
        self::assertEquals(EventObjectInfo::$FREE, $objectInfo1->status);
    }

    public function testOverrideSeasonStatus()
    {
        $chartKey = $this->createTestChart();
        $season = $this->seatsioClient->seasons->create($chartKey, (new SeasonCreationParams())->setEventKeys(["anEvent"]));
        $this->seatsioClient->events->book($season->key, ["A-1", "A-2"]);

        $response = $this->seatsioClient->events->changeObjectStatusInBatch([
            (new StatusChangeRequest())->setType(StatusChangeRequest::$TYPE_OVERRIDE_SEASON_STATUS)->setEvent("anEvent")->setObjects(["A-1", "A-2"]),
        ]);

        self::assertEquals(EventObjectInfo::$FREE, $response[0]->objects['A-1']->status);
        self::assertEquals(EventObjectInfo::$FREE, $response[0]->objects['A-2']->status);
    }

    public function testUseSeasonStatus()
    {
        $chartKey = $this->createTestChart();
        $season = $this->seatsioClient->seasons->create($chartKey, (new SeasonCreationParams())->setEventKeys(["anEvent"]));
        $this->seatsioClient->events->book($season->key, ["A-1", "A-2"]);
        $this->seatsioClient->events->overrideSeasonStatus("anEvent", ["A-1", "A-2"]);

        $response = $this->seatsioClient->events->changeObjectStatusInBatch([
            (new StatusChangeRequest())->setType(StatusChangeRequest::$TYPE_USE_SEASON_STATUS)->setEvent("anEvent")->setObjects(["A-1", "A-2"]),
        ]);

        self::assertEquals(EventObjectInfo::$BOOKED, $response[0]->objects['A-1']->status);
        self::assertEquals(EventObjectInfo::$BOOKED, $response[0]->objects['A-2']->status);
    }

    public function testResaleListingId()
    {
        $chartKey1 = $this->createTestChart();
        $chartKey2 = $this->createTestChart();
        $event1 = $this->seatsioClient->events->create($chartKey1);
        $event2 = $this->seatsioClient->events->create($chartKey2);

        $response = $this->seatsioClient->events->changeObjectStatusInBatch([
            (new StatusChangeRequest())->setEvent($event1->key)->setObjects("A-1")->setStatus(EventObjectInfo::$RESALE)->setResaleListingId("listing1"),
            (new StatusChangeRequest())->setEvent($event2->key)->setObjects("A-2")->setStatus(EventObjectInfo::$RESALE)->setResaleListingId("listing1")
        ]);

        $objectInfo1 = $this->seatsioClient->events->retrieveObjectInfo($event1->key, "A-1");
        self::assertEquals("listing1", $objectInfo1->resaleListingId);

        $objectInfo2 = $this->seatsioClient->events->retrieveObjectInfo($event2->key, "A-2");
        self::assertEquals("listing1", $objectInfo2->resaleListingId);
    }
}
