<?php

namespace Seatsio\Charts;

use Seatsio\SeatsioClientTest;

class RetrieveDraftVersionThumbnailTest extends SeatsioClientTest
{

    public function test()
    {
        $chart = $this->seatsioClient->charts->create();
        $this->seatsioClient->events->create($chart->key);
        $this->seatsioClient->charts->update($chart->key, 'newName');

        $retrievedChartThumbnail = $this->seatsioClient->charts->retrieveDraftVersionThumbnail($chart->key);

        self::assertNotEmpty($retrievedChartThumbnail);
    }

}
