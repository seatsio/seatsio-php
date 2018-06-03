<?php

namespace Seatsio\Charts;

use Seatsio\SeatsioClientTest;

class ListAllTagsTest extends SeatsioClientTest
{

    public function test()
    {
        $chart1 = $this->seatsioClient->charts->create();
        $this->seatsioClient->charts->addTag($chart1->key, 'tag1');

        $chart2 = $this->seatsioClient->charts->create();
        $this->seatsioClient->charts->addTag($chart2->key, 'tag2');

        self::assertEquals(['tag2', 'tag1'], $this->seatsioClient->charts->listAllTags(), '', 0.0, 10, true);
    }

}