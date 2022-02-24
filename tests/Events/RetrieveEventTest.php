<?php

namespace Seatsio\Events;

use Seatsio\Seasons\SeasonCreationParams;
use Seatsio\SeatsioClientTest;

class RetrieveEventTest extends SeatsioClientTest
{

    public function test()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey);

        $retrievedEvent = $this->seatsioClient->events->retrieve($event->key);

        self::assertEquals($event->key, $retrievedEvent->key);
        self::assertEquals(false, $retrievedEvent->isEventInSeason);
        self::assertEquals($event->id, $retrievedEvent->id);
        self::assertEquals($chartKey, $retrievedEvent->chartKey);
        self::assertEquals(TableBookingConfig::inherit(), $retrievedEvent->tableBookingConfig);
        self::assertTrue($retrievedEvent->supportsBestAvailable);
        self::assertEquals($event->createdOn, $retrievedEvent->createdOn);
        self::assertNull($retrievedEvent->forSaleConfig);
        self::assertNull($retrievedEvent->updatedOn);
    }

    public function testRetrieveSeason()
    {
        $chartKey = $this->createTestChart();
        $season = $this->seatsioClient->seasons->create($chartKey, new SeasonCreationParams('aSeason'));
        $this->seatsioClient->seasons->createPartialSeason($season->key, 'partialSeason1');
        $this->seatsioClient->seasons->createPartialSeason($season->key, 'partialSeason2');

        $retrievedSeason = $this->seatsioClient->events->retrieve($season->key);

        self::assertEquals('aSeason', $retrievedSeason->key);
        self::assertEquals(true, $retrievedSeason->isTopLevelSeason);
        self::assertEquals(['partialSeason1', 'partialSeason2'], $retrievedSeason->partialSeasonKeys);
    }

}
