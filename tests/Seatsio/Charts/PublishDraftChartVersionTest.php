<?php

namespace Seatsio\Charts;

use Seatsio\SeatsioClientTest;

class PublishDraftChartVersionTest extends SeatsioClientTest
{

    public function test()
    {
        $chart = $this->seatsioClient->charts()->create('oldName');
        $this->seatsioClient->events()->create($chart->key);
        $this->seatsioClient->charts()->update($chart->key, 'newName');

        $this->seatsioClient->charts()->publishDraft($chart->key);

        $retrievedChart = $this->seatsioClient->charts()->retrieve($chart->key);
        self::assertEquals('PUBLISHED', $retrievedChart->status);
        self::assertEquals('newName', $retrievedChart->name);
    }

}