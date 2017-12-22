<?php

namespace Seatsio\Charts;

use Seatsio\SeatsioClientTest;

class RetrieveChartTest extends SeatsioClientTest
{

    public function test()
    {
        $chart = $this->seatsioClient->charts()->create();
        $this->seatsioClient->charts()->addTag($chart->key, "tag1");

        $retrievedChart = $this->seatsioClient->charts()->retrieve($chart->key);

        self::assertEquals($chart->key, $retrievedChart->key);
        self::assertNotNull($retrievedChart->id);
        self::assertEquals('Untitled chart', $retrievedChart->name);
        self::assertEquals('NOT_USED', $retrievedChart->status);
        self::assertNotNull($retrievedChart->publishedVersionThumbnailUrl);
        self::assertNull($retrievedChart->draftVersionThumbnailUrl);
        self::assertEquals(['tag1'], $retrievedChart->tags);
        self::assertFalse($retrievedChart->archived);
    }

}