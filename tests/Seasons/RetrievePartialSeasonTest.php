<?php

namespace Seatsio\Seasons;

use Seatsio\Events\TableBookingConfig;
use Seatsio\SeatsioClientTest;

class RetrievePartialSeasonTest extends SeatsioClientTest
{
    public function test()
    {
        $chartKey = $this->createTestChart();
        $season = $this->seatsioClient->seasons->create($chartKey);
        $this->seatsioClient->seasons->createPartialSeason($season->key, 'aPartialSeason');

        $partialSeason = $this->seatsioClient->seasons->retrievePartialSeason($season->key, 'aPartialSeason');

        self::assertEquals('aPartialSeason', $partialSeason->key);
        self::assertNotNull($partialSeason->id);

        $seasonEvent = $partialSeason->seasonEvent;
        self::assertEquals($seasonEvent->key, 'aPartialSeason');
        self::assertNotNull($seasonEvent->id);
        self::assertEquals($chartKey, $seasonEvent->chartKey);
        self::assertEquals(TableBookingConfig::inherit(), $seasonEvent->tableBookingConfig);
        self::assertTrue($seasonEvent->supportsBestAvailable);
        self::assertNotNull($seasonEvent->createdOn);
        self::assertNull($seasonEvent->forSaleConfig);
        self::assertNull($seasonEvent->updatedOn);
    }
}
