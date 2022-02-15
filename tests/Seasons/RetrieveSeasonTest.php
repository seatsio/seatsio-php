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
        self::assertEquals($chartKey, $retrievedSeason->chartKey);
        self::assertEquals(TableBookingConfig::inherit(), $retrievedSeason->tableBookingConfig);
        self::assertTrue($retrievedSeason->supportsBestAvailable);
        self::assertNotNull($retrievedSeason->createdOn);
        self::assertNull($retrievedSeason->forSaleConfig);
        self::assertNull($retrievedSeason->updatedOn);
    }
}
