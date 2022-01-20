<?php

namespace Seatsio\Seasons;

use Seatsio\Events\TableBookingConfig;
use Seatsio\SeatsioClientTest;

class DeleteSeasonTest extends SeatsioClientTest
{
    /**
     * @expectedException \Seatsio\SeatsioException
     */
    public function test()
    {
        $chartKey = $this->createTestChart();
        $season = $this->seatsioClient->seasons->create($chartKey);

        $this->seatsioClient->seasons->delete($season->key);

        $this->seatsioClient->seasons->retrieve($season->key);
    }
}
