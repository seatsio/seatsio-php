<?php

namespace Seatsio\Charts;

use Seatsio\SeatsioClientTest;

class RetrievePublishedVersionTest extends SeatsioClientTest
{

    public function test()
    {
        $chart = $this->seatsioClient->charts->create();

        $retrievedChart = $this->seatsioClient->charts->retrievePublishedVersion($chart->key);

        self::assertEquals('Untitled chart', $retrievedChart->name);
        self::assertEquals('SIMPLE', $retrievedChart->venueType);
        self::assertEmpty($retrievedChart->categories->list);
        self::assertNotEmpty($retrievedChart->subChart);
    }

}
