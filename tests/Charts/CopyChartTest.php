<?php

namespace Seatsio\Charts;

use Seatsio\SeatsioClientTest;

class CopyChartTest extends SeatsioClientTest
{

    public function test()
    {
        $chart = $this->seatsioClient->charts->create('my chart', 'SIMPLE');

        $copiedChart = $this->seatsioClient->charts->copy($chart->key);

        self::assertEquals('my chart (copy)', $copiedChart->name);
        self::assertNotEquals($chart->key, $copiedChart->key);
        $retrievedChart = $this->seatsioClient->charts->retrievePublishedVersion($copiedChart->key);
        self::assertEquals('SIMPLE', $retrievedChart->venueType);
    }

}
