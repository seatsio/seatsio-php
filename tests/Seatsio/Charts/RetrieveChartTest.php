<?php

namespace Seatsio;

class RetrieveChartTest extends SeatsioClientTest
{

    public function testRetrieveChart()
    {
        $chartKey = $this->seatsioClient->createChart();

        $retrievedChart = $this->seatsioClient->retrieveChart($chartKey);

        self::assertEquals('Untitled chart', $retrievedChart->name);
        self::assertEquals('MIXED', $retrievedChart->venueType);
        self::assertEmpty($retrievedChart->categories->list);
        self::assertNotEmpty($retrievedChart->subChart);
    }

    /**
     * @expectedException \GuzzleHttp\Exception\ClientException
     */
    public function testRetrieveChartThatDoesNotExist()
    {
        $this->seatsioClient->retrieveChart('unexistingChart');
    }

}