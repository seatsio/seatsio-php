<?php

namespace Seatsio\Charts;

use Seatsio\SeatsioClientTest;

class RetrievePublishedVersionThumbnailTest extends SeatsioClientTest
{

    public function test()
    {
        $chart = $this->seatsioClient->charts->create();

        $retrievedChartThumbnail = $this->seatsioClient->charts->retrievePublishedVersionThumbnail($chart->key);

        self::assertContains('<!DOCTYPE svg', (string)$retrievedChartThumbnail);
    }

}