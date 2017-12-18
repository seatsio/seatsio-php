<?php

namespace Seatsio\Charts;

use Seatsio\SeatsioClientTest;

class DiscardDraftChartVersionTest extends SeatsioClientTest
{

    public function test()
    {
        $chart = $this->seatsioClient->charts()->create('oldName');
        $this->seatsioClient->events()->create($chart->key);
        $this->seatsioClient->charts()->update($chart->key, 'newName');

        $this->seatsioClient->charts()->discardDraft($chart->key);

        $retrievedChart = $this->seatsioClient->charts()->retrieve($chart->key);
        self::assertEquals('PUBLISHED', $retrievedChart->status);
        self::assertEquals('oldName', $retrievedChart->name);
    }

}