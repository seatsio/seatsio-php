<?php

namespace Seatsio\Seasons;

use Seatsio\Events\TableBookingConfig;
use Seatsio\SeatsioClientTest;

class RetrieveSeasonTest extends SeatsioClientTest
{
    public function test()
    {
        $chartKey = $this->createTestChart();
        $season = $this->seatsioClient->seasons->create($chartKey, new SeasonCreationParams('aSeason'));
        $this->seatsioClient->seasons->createPartialSeason($season->key, 'partialSeason1');
        $this->seatsioClient->seasons->createPartialSeason($season->key, 'partialSeason2');

        $retrievedSeason = $this->seatsioClient->seasons->retrieve($season->key);

        self::assertEquals('aSeason', $retrievedSeason->key);
        self::assertNotNull($retrievedSeason->id);
        self::assertEquals(['partialSeason1', 'partialSeason2'], $retrievedSeason->partialSeasonKeys);

        $seasonEvent = $retrievedSeason->seasonEvent;
        self::assertEquals('aSeason', $retrievedSeason->key);
        self::assertNotNull($seasonEvent->id);
        self::assertEquals($chartKey, $seasonEvent->chartKey);
        self::assertEquals(TableBookingConfig::inherit(), $seasonEvent->tableBookingConfig);
        self::assertTrue($seasonEvent->supportsBestAvailable);
        self::assertNotNull($seasonEvent->createdOn);
        self::assertNull($seasonEvent->forSaleConfig);
        self::assertNull($seasonEvent->updatedOn);
    }
}
