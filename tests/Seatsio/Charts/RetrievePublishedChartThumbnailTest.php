<?php

namespace Seatsio\Charts;

use Seatsio\SeatsioClientTest;

class RetrievePublishedChartThumbnailTest extends SeatsioClientTest
{

    public function test()
    {
        $chart = $this->seatsioClient->charts()->create();

        $retrievedChartThumbnail = $this->seatsioClient->charts()->retrieveThumbnail($chart->key);

        self::assertContains('<!DOCTYPE svg', (string)$retrievedChartThumbnail);
    }

}