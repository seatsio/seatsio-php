<?php

namespace Seatsio\Charts;

use Seatsio\SeatsioClientTest;

class RetrieveChartTest extends SeatsioClientTest
{

    public function test()
    {
        $chart = $this->seatsioClient->charts->create();
        $this->seatsioClient->charts->addTag($chart->key, "tag1");

        $retrievedChart = $this->seatsioClient->charts->retrieve($chart->key);

        self::assertEquals($chart->key, $retrievedChart->key);
        self::assertNotNull($retrievedChart->id);
        self::assertEquals('Untitled chart', $retrievedChart->name);
        self::assertEquals('NOT_USED', $retrievedChart->status);
        self::assertNotNull($retrievedChart->publishedVersionThumbnailUrl);
        self::assertNull($retrievedChart->draftVersionThumbnailUrl);
        self::assertEquals(['tag1'], $retrievedChart->tags);
        self::assertFalse($retrievedChart->archived);
        self::assertNull($retrievedChart->events);
    }

    public function testRetrieveWithEvents()
    {
        $chart = $this->seatsioClient->charts->create();
        $event1 = $this->seatsioClient->events->create($chart->key);
        $event2 = $this->seatsioClient->events->create($chart->key);

        $retrievedChart = $this->seatsioClient->charts->retrieveWithEvents($chart->key);

        $eventIds = \Functional\map($retrievedChart->events, function ($event) {
            return $event->id;
        });

        self::assertEquals([$event2->id, $event1->id], array_values($eventIds));
    }

}