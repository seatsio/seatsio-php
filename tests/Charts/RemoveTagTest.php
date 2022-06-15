<?php

namespace Seatsio\Charts;

use Seatsio\SeatsioClientTest;

class RemoveTagTest extends SeatsioClientTest
{

    public function test()
    {
        $chart = $this->seatsioClient->charts->create();
        $this->seatsioClient->charts->addTag($chart->key, 'tag1');
        $this->seatsioClient->charts->addTag($chart->key, 'tag2');

        $this->seatsioClient->charts->removeTag($chart->key, 'tag1');

        $retrievedChart = $this->seatsioClient->charts->retrieve($chart->key);
        self::assertEquals(['tag2'], $retrievedChart->tags);
    }

}
