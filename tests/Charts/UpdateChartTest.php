<?php

namespace Seatsio\Charts;

use Seatsio\SeatsioClientTest;

class UpdateChartTest extends SeatsioClientTest
{

    public function testUpdateName()
    {
        $categories = [
            (object)['key' => 1, 'label' => 'Category 1', 'color' => '#aaaaaa', 'accessible' => false]
        ];
        $chart = $this->seatsioClient->charts->create(null, null, $categories);

        $this->seatsioClient->charts->update($chart->key, 'aChart');

        $reloadedChart = $this->seatsioClient->charts->retrieve($chart->key);
        self::assertEquals('aChart', $reloadedChart->name);
    }

    public function testUpdateCategories()
    {
        $chart = $this->seatsioClient->charts->create('aChart');
        $categories = [
            (object)['key' => 1, 'label' => 'Category 1', 'color' => '#aaaaaa', 'accessible' => true]
        ];

        $this->seatsioClient->charts->update($chart->key, null, $categories);

        $reloadedChart = $this->seatsioClient->charts->retrieve($chart->key);
        self::assertEquals('aChart', $reloadedChart->name);
    }

}
