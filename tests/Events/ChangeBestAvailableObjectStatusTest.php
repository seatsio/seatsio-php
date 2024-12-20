<?php

namespace Seatsio\Events;

use Seatsio\BestAvailableObjectsNotFoundException;
use Seatsio\Common\IDs;
use Seatsio\SeatsioClientTest;
use Seatsio\SeatsioException;

class ChangeBestAvailableObjectStatusTest extends SeatsioClientTest
{

    public function test()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey);

        $bestAvailableObjects = $this->seatsioClient->events->changeBestAvailableObjectStatus($event->key, (new BestAvailableParams())->setNumber(2), "lolzor");

        self::assertTrue($bestAvailableObjects->nextToEachOther);
        self::assertEquals(["A-4", "A-5"], $bestAvailableObjects->objects);
    }

    public function testObjectDetails()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey);

        $bestAvailableObjects = $this->seatsioClient->events->changeBestAvailableObjectStatus($event->key, (new BestAvailableParams())->setNumber(2), "lolzor");

        $objectDetails = $bestAvailableObjects->objectDetails["A-5"];
        self::assertEquals("lolzor", $objectDetails->status);
        self::assertEquals("A-5", $objectDetails->label);
        self::assertEquals(someLabels("5", "seat", "A", "row"), $objectDetails->labels);
        self::assertEquals(new IDs("5", "A", null), $objectDetails->ids);
        self::assertEquals("Cat1", $objectDetails->categoryLabel);
        self::assertEquals(9, $objectDetails->categoryKey);
        self::assertNull($objectDetails->ticketType);
        self::assertNull($objectDetails->orderId);
        self::assertEquals("seat", $objectDetails->objectType);
        self::assertTrue($objectDetails->forSale);
        self::assertNull($objectDetails->section);
        self::assertNull($objectDetails->entrance);
        self::assertEquals("A-4", $objectDetails->leftNeighbour);
        self::assertEquals("A-6", $objectDetails->rightNeighbour);
    }

    public function testCategories()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey);

        $bestAvailableObjects = $this->seatsioClient->events->changeBestAvailableObjectStatus($event->key, (new BestAvailableParams())->setNumber(3)->setCategories(["cat2"]), "lolzor");

        self::assertEquals(["C-4", "C-5", "C-6"], $bestAvailableObjects->objects);
    }

    public function testZones()
    {
        $chartKey = $this->createTestChartWithZones();
        $event = $this->seatsioClient->events->create($chartKey);

        $bestAvailableObjectsMidtrack = $this->seatsioClient->events->changeBestAvailableObjectStatus($event->key, (new BestAvailableParams())->setNumber(1)->setZone("midtrack"), "lolzor");
        self::assertEquals(["MT3-A-139"], $bestAvailableObjectsMidtrack->objects);

        $bestAvailableObjectsFinishline = $this->seatsioClient->events->changeBestAvailableObjectStatus($event->key, (new BestAvailableParams())->setNumber(1)->setZone("finishline"), "lolzor");
        self::assertEquals(["Goal Stand 4-A-1"], $bestAvailableObjectsFinishline->objects);
    }

    public function testExtraData()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey);
        $extraData = [
            ["foo" => "bar"],
            ["foo" => "baz"]
        ];

        $bestAvailableObjects = $this->seatsioClient->events->changeBestAvailableObjectStatus($event->key, (new BestAvailableParams())->setNumber(2)->setExtraData($extraData), "lolzor");

        self::assertEquals(["A-4", "A-5"], $bestAvailableObjects->objects);
        $b4ObjectInfo = $this->seatsioClient->events->retrieveObjectInfo($event->key, "A-4");
        self::assertEquals($b4ObjectInfo->extraData, (object)["foo" => "bar"]);
        $b5ObjectInfo = $this->seatsioClient->events->retrieveObjectInfo($event->key, "A-5");
        self::assertEquals($b5ObjectInfo->extraData, (object)["foo" => "baz"]);
    }

    public function testDoNotTryToPreventOrphanSeats()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey);
        $this->seatsioClient->events->book($event->key, ["A-4", "A-5"]);

        $bestAvailableObjects = $this->seatsioClient->events->changeBestAvailableObjectStatus($event->key, (new BestAvailableParams())->setNumber(2)->setTryToPreventOrphanSeats(false), "lolzor");

        self::assertEquals(["A-2", "A-3"], $bestAvailableObjects->objects);
    }

    public function testTicketTypes()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey);

        $bestAvailableObjects = $this->seatsioClient->events->changeBestAvailableObjectStatus($event->key, (new BestAvailableParams())->setNumber(2)->setTicketTypes(["adult", "child"]), "lolzor");

        self::assertEquals(["A-4", "A-5"], $bestAvailableObjects->objects);
        $b4ObjectInfo = $this->seatsioClient->events->retrieveObjectInfo($event->key, "A-4");
        self::assertEquals($b4ObjectInfo->ticketType, "adult");
        $b5ObjectInfo = $this->seatsioClient->events->retrieveObjectInfo($event->key, "A-5");
        self::assertEquals($b5ObjectInfo->ticketType, "child");
    }

    public function testHoldToken()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey);
        $holdToken = $this->seatsioClient->holdTokens->create();

        $bestAvailableObjects = $this->seatsioClient->events->changeBestAvailableObjectStatus($event->key, (new BestAvailableParams())->setNumber(1), EventObjectInfo::$HELD, $holdToken->holdToken);

        $objectInfo = $this->seatsioClient->events->retrieveObjectInfo($event->key, $bestAvailableObjects->objects[0]);
        self::assertEquals(EventObjectInfo::$HELD, $objectInfo->status);
        self::assertEquals($holdToken->holdToken, $objectInfo->holdToken);
    }

    public function testOrderId()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey);

        $bestAvailableObjects = $this->seatsioClient->events->changeBestAvailableObjectStatus($event->key, (new BestAvailableParams())->setNumber(1), "lolzor", null, "anOrder");

        $objectInfo = $this->seatsioClient->events->retrieveObjectInfo($event->key, $bestAvailableObjects->objects[0]);
        self::assertEquals("anOrder", $objectInfo->orderId);
    }

    public function testBook()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey);

        $bestAvailableObjects = $this->seatsioClient->events->bookBestAvailable($event->key, (new BestAvailableParams())->setNumber(3));

        self::assertTrue($bestAvailableObjects->nextToEachOther);
        self::assertEquals(["A-4", "A-5", "A-6"], $bestAvailableObjects->objects);
    }

    public function testHold()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey);
        $holdToken = $this->seatsioClient->holdTokens->create();

        $bestAvailableObjects = $this->seatsioClient->events->holdBestAvailable($event->key, (new BestAvailableParams())->setNumber(1), $holdToken->holdToken);

        $objectInfo = $this->seatsioClient->events->retrieveObjectInfo($event->key, $bestAvailableObjects->objects[0]);
        self::assertEquals(EventObjectInfo::$HELD, $objectInfo->status);
        self::assertEquals($holdToken->holdToken, $objectInfo->holdToken);
    }

    public function testKeepExtraData()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey);
        $extraData = ["foo" => "bar"];
        $this->seatsioClient->events->updateExtraData($event->key, "A-5", $extraData);

        $this->seatsioClient->events->changeBestAvailableObjectStatus($event->key, (new BestAvailableParams())->setNumber(1), "lolzor", null, null, true);

        $objectInfo = $this->seatsioClient->events->retrieveObjectInfo($event->key, "A-5");
        self::assertEquals((object)$extraData, $objectInfo->extraData);
    }

    public function testChannelKeys()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey, (new CreateEventParams())->setChannels([
            new Channel("channelKey1", "channel 1", "#FF0000", 1, ["B-6"])
        ]));

        $bestAvailableObjects = $this->seatsioClient->events->changeBestAvailableObjectStatus($event->key, (new BestAvailableParams())->setNumber(1), "lolzor", null, null, null, null, ["channelKey1"]);

        self::assertEquals(["B-6"], $bestAvailableObjects->objects);
    }

    public function testIgnoreChannels()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey, (new CreateEventParams())->setChannels([
            new Channel("channelKey1", "channel 1", "#FF0000", 1, ["A-5"])
        ]));

        $bestAvailableObjects = $this->seatsioClient->events->changeBestAvailableObjectStatus($event->key, (new BestAvailableParams())->setNumber(1), "lolzor", null, null, null, true);

        self::assertEquals(["A-5"], $bestAvailableObjects->objects);
    }

    public function accessibleSeats()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey);

        $bestAvailableObjects = $this->seatsioClient->events->changeBestAvailableObjectStatus($event->key, (new BestAvailableParams())->setNumber(3)->setAccessibleSeats(1), "lolzor");

        self::assertTrue($bestAvailableObjects->nextToEachOther);
        self::assertEquals(["A-6", "A-7", "A-8"], $bestAvailableObjects->objects);
    }

    public function testNotFoundThrowsBestAvailableObjectsNotFoundException()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey);

        try
        {
            $this->seatsioClient->events->changeBestAvailableObjectStatus($event->key, (new BestAvailableParams())->setNumber(3000), "lolzor");
            self::fail("expected exception");
        }  catch (BestAvailableObjectsNotFoundException $exception) {
            self::assertInstanceOf(BestAvailableObjectsNotFoundException::class, $exception);
            self::assertEquals("Best available objects not found", $exception->getMessage());
        }

    }

    public function testNormalSeatsioExceptionIfEventNotFound()
    {
        try
        {
            $this->seatsioClient->events->changeBestAvailableObjectStatus("unexistingEvent", (new BestAvailableParams())->setNumber(3), "lolzor");
            self::fail("expected exception");
        }  catch (SeatsioException $exception) {
            self::assertNotInstanceOf(BestAvailableObjectsNotFoundException::class, $exception);
        }

    }
}
