<?php

namespace Seatsio\Charts;

use Seatsio\SeatsioClientTest;

class ListTagsTest extends SeatsioClientTest
{

    public function test()
    {
        $chart = $this->seatsioClient->charts()->create();
        $this->seatsioClient->charts()->addTag($chart->key, 'tag1');
        $this->seatsioClient->charts()->addTag($chart->key, 'tag2');

        self::assertEquals(['tag2', 'tag1'], $this->seatsioClient->charts()->listTags($chart->key), '', 0.0, 10, true);
    }

}