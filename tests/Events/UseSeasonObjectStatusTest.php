<?php

namespace Events;

use Seatsio\Common\IDs;
use Seatsio\Events\Channel;
use Seatsio\Events\CreateEventParams;
use Seatsio\Events\EventObjectInfo;
use Seatsio\Seasons\SeasonCreationParams;
use Seatsio\SeatsioClientTest;

class UseSeasonObjectStatusTest extends SeatsioClientTest
{

    public function test()
    {
        $chartKey = $this->createTestChart();
        $season = $this->seatsioClient->seasons->create($chartKey, (new SeasonCreationParams())->setEventKeys(["anEvent"]));
        $this->seatsioClient->events->book($season->key, ["A-1", "A-2"]);
        $this->seatsioClient->events->overrideSeasonStatus("anEvent", ["A-1", "A-2"]);

        $this->seatsioClient->events->useSeasonStatus("anEvent", ["A-1", "A-2"]);

        self::assertEquals(EventObjectInfo::$BOOKED, $this->seatsioClient->events->retrieveObjectInfo("anEvent", "A-1")->status);
        self::assertEquals(EventObjectInfo::$BOOKED, $this->seatsioClient->events->retrieveObjectInfo("anEvent", "A-2")->status);
    }
}
