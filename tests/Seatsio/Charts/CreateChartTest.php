<?php

namespace Seatsio;

class CreateChartTest extends SeatsioClientTest
{

    public function testCreateChartWithDefaultParameters()
    {
        $chart = $this->seatsioClient->createChart();

        $retrievedChart = $this->seatsioClient->retrieveChart($chart->key);
        self::assertEquals('Untitled chart', $retrievedChart->name);
        self::assertEquals('MIXED', $retrievedChart->venueType);
        self::assertEmpty($retrievedChart->categories->list);
    }

    public function testCreateChartWithName()
    {
        $chart = $this->seatsioClient->createChart('aChart');

        $retrievedChart = $this->seatsioClient->retrieveChart($chart->key);
        self::assertEquals('aChart', $retrievedChart->name);
        self::assertEquals('MIXED', $retrievedChart->venueType);
        self::assertEmpty($retrievedChart->categories->list);
    }

    public function testCreateChartWithVenueType()
    {
        $chart = $this->seatsioClient->createChart(null, 'BOOTHS');

        $retrievedChart = $this->seatsioClient->retrieveChart($chart->key);
        self::assertEquals('Untitled chart', $retrievedChart->name);
        self::assertEquals('BOOTHS', $retrievedChart->venueType);
        self::assertEmpty($retrievedChart->categories->list);
    }

    public function testCreateChartWithCategories()
    {
        $categories = [
            (object)['key' => 1, 'label' => 'Category 1', 'color' => '#aaaaaa'],
            (object)['key' => 2, 'label' => 'Category 2', 'color' => '#bbbbbb']
        ];

        $chart = $this->seatsioClient->createChart(null, null, $categories);

        $retrievedChart = $this->seatsioClient->retrieveChart($chart->key);
        self::assertEquals('Untitled chart', $retrievedChart->name);
        self::assertEquals('MIXED', $retrievedChart->venueType);
        self::assertEquals($categories, $retrievedChart->categories->list);
    }

}