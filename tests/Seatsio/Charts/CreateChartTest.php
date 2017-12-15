<?php

namespace Seatsio\Charts;

use Seatsio\SeatsioClientTest;

class CreateChartTest extends SeatsioClientTest
{

    public function testCreateChartWithDefaultParameters()
    {
        $chart = $this->seatsioClient->charts()->create();

        $retrievedChart = $this->seatsioClient->charts()->retrieve($chart->key);
        self::assertEquals('Untitled chart', $retrievedChart->name);
        self::assertEquals('MIXED', $retrievedChart->venueType);
        self::assertEmpty($retrievedChart->categories->list);
    }

    public function testCreateChartWithName()
    {
        $chart = $this->seatsioClient->charts()->create('aChart');

        $retrievedChart = $this->seatsioClient->charts()->retrieve($chart->key);
        self::assertEquals('aChart', $retrievedChart->name);
        self::assertEquals('MIXED', $retrievedChart->venueType);
        self::assertEmpty($retrievedChart->categories->list);
    }

    public function testCreateChartWithVenueType()
    {
        $chart = $this->seatsioClient->charts()->create(null, 'BOOTHS');

        $retrievedChart = $this->seatsioClient->charts()->retrieve($chart->key);
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

        $chart = $this->seatsioClient->charts()->create(null, null, $categories);

        $retrievedChart = $this->seatsioClient->charts()->retrieve($chart->key);
        self::assertEquals('Untitled chart', $retrievedChart->name);
        self::assertEquals('MIXED', $retrievedChart->venueType);
        self::assertEquals($categories, $retrievedChart->categories->list);
    }

}