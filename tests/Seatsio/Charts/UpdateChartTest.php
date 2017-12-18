<?php

namespace Seatsio\Charts;

use Seatsio\SeatsioClientTest;

class UpdateChartTest extends SeatsioClientTest
{

    public function testUpdateName()
    {
        $categories = [
            (object)['key' => 1, 'label' => 'Category 1', 'color' => '#aaaaaa']
        ];
        $chart = $this->seatsioClient->charts()->create(null, null, $categories);

        $this->seatsioClient->charts()->update($chart->key, 'aChart');

        $retrievedChart = $this->seatsioClient->charts()->retrievePublishedChartVersion($chart->key);
        self::assertEquals('aChart', $retrievedChart->name);
        self::assertEquals($categories, $retrievedChart->categories->list);
    }

    public function testUpdateCategories()
    {
        $chart = $this->seatsioClient->charts()->create('aChart');
        $categories = [
            (object)['key' => 1, 'label' => 'Category 1', 'color' => '#aaaaaa']
        ];

        $this->seatsioClient->charts()->update($chart->key, null, $categories);

        $retrievedChart = $this->seatsioClient->charts()->retrievePublishedChartVersion($chart->key);
        self::assertEquals('aChart', $retrievedChart->name);
        self::assertEquals($categories, $retrievedChart->categories->list);
    }

}