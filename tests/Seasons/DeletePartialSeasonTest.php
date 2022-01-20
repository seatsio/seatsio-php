<?php

namespace Seatsio\Seasons;

use Seatsio\Events\TableBookingConfig;
use Seatsio\SeatsioClientTest;

class DeletePartialSeasonTest extends SeatsioClientTest
{
    /**
     * @expectedException \Seatsio\SeatsioException
     */
    public function test()
    {
        $chartKey = $this->createTestChart();
        $season = $this->seatsioClient->seasons->create($chartKey);

        $this->seatsioClient->seasons->deletePartialSeason($season->key, 'aPartialSeason');

        $this->seatsioClient->seasons->retrievePartialSeason($season->key, 'aPartialSeason');
    }
}
