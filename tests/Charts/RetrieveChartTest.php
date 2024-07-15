<?php

namespace Seatsio\Charts;

use Seatsio\SeatsioClientTest;
use function Functional\map;

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
        self::assertNull($retrievedChart->zones);
    }

    public function zones()
    {
        $chart = $this->createTestChartWithZones();

        $retrievedChart = $this->seatsioClient->charts->retrieve($chart);

        self::assertEquals([new Zone("finishline", "Finish Line"), new Zone("midtrack", "Mid Track")], $retrievedChart->zones);
    }

    public function testRetrieveWithEvents()
    {
        $chart = $this->seatsioClient->charts->create();
        $event1 = $this->seatsioClient->events->create($chart->key);
        $event2 = $this->seatsioClient->events->create($chart->key);

        $retrievedChart = $this->seatsioClient->charts->retrieveWithEvents($chart->key);

        $eventIds = map($retrievedChart->events, function ($event) {
            return $event->id;
        });

        self::assertEquals([$event2->id, $event1->id], array_values($eventIds));
    }

}
