<?php

namespace Seatsio\Seasons;

use Seatsio\SeatsioClientTest;
use function Functional\map;

class ListSeasonsTest extends SeatsioClientTest
{
    public function test()
    {
        $chartKey = $this->createTestChart();
        $this->seatsioClient->seasons->create($chartKey, new SeasonCreationParams('season1'));
        $this->seatsioClient->seasons->create($chartKey, new SeasonCreationParams('season2'));
        $this->seatsioClient->seasons->create($chartKey, new SeasonCreationParams('season3'));

        $seasons = $this->seatsioClient->seasons->listAll();
        $seasonKeys = map($seasons, function ($event) {
            return $event->key;
        });

        self::assertEquals(['season3', 'season2', 'season1'], array_values($seasonKeys));
    }
}
