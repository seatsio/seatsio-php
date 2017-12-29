<?php

namespace Seatsio\Charts;

use Seatsio\SeatsioClientTest;

class AddTagTest extends SeatsioClientTest
{

    public function test()
    {
        $chart = $this->seatsioClient->charts()->create();

        $this->seatsioClient->charts()->addTag($chart->key, 'tag1');

        $retrievedChart = $this->seatsioClient->charts()->retrieve($chart->key);
        self::assertEquals(['tag1'], $retrievedChart->tags, '', 0.0, 10, true);
    }

    public function testSpecialCharacters()
    {
        $chart = $this->seatsioClient->charts()->create();

        $this->seatsioClient->charts()->addTag($chart->key, 'tag1/:"-<>');

        $retrievedChart = $this->seatsioClient->charts()->retrieve($chart->key);
        self::assertEquals(['tag1/:"-<>'], $retrievedChart->tags, '', 0.0, 10, true);
    }

}