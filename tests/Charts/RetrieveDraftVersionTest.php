<?php

namespace Seatsio\Charts;

use Seatsio\SeatsioClientTest;

class RetrieveDraftVersionTest extends SeatsioClientTest
{

    public function test()
    {
        $chart = $this->seatsioClient->charts->create();
        $this->seatsioClient->events->create($chart->key);
        $this->seatsioClient->charts->update($chart->key, 'newName');

        $retrievedChart = $this->seatsioClient->charts->retrieveDraftVersion($chart->key);

        self::assertEquals('newName', $retrievedChart->name);
    }

}