<?php

namespace Seatsio\Charts;

use Seatsio\SeatsioClientTest;

class RetrievePublishedChartTest extends SeatsioClientTest
{

    public function test()
    {
        $chart = $this->seatsioClient->charts()->create();

        $retrievedChart = $this->seatsioClient->charts()->retrievePublishedChart($chart->key);

        self::assertEquals('Untitled chart', $retrievedChart->name);
        self::assertEquals('MIXED', $retrievedChart->venueType);
        self::assertEmpty($retrievedChart->categories->list);
        self::assertNotEmpty($retrievedChart->subChart);
    }

}