<?php

namespace Seatsio\Charts;

use Seatsio\SeatsioClientTest;

class CreateChartTest extends SeatsioClientTest
{

    public function testCreateChartWithDefaultParameters()
    {
        $chart = $this->seatsioClient->charts->create();

        self::assertNotEmpty($chart->id);
        self::assertNotEmpty($chart->key);
        self::assertEquals('NOT_USED', $chart->status);
        self::assertEquals('Untitled chart', $chart->name);
        self::assertNotEmpty($chart->publishedVersionThumbnailUrl);
        self::assertEmpty($chart->draftVersionThumbnailUrl);
        self::assertEmpty($chart->tags);
        self::assertFalse($chart->archived);
    }

    public function testCreateChartWithName()
    {
        $chart = $this->seatsioClient->charts->create('aChart');

        self::assertEquals('aChart', $chart->name);
    }

    public function testCreateChartWithVenueType()
    {
        $chart = $this->seatsioClient->charts->create(null, 'BOOTHS');
        self::assertNotEmpty($chart->name);
    }

    public function testCreateChartWithCategoriesAsAssociativeArray()
    {
        $cat1 = ['key' => 1, 'label' => 'Category 1', 'color' => '#aaaaaa', 'accessible' => false];
        $cat2 = ['key' => 2, 'label' => 'Category 2', 'color' => '#bbbbbb', 'accessible' => true];

        $chart = $this->seatsioClient->charts->create(null, null, [$cat1, $cat2]);

        self::assertNotEmpty($chart->name);
    }

    public function testCreateChartWithCategoriesAsObjects()
    {
        $cat1 = (new CategoryRequestBuilder())->setKey(1)->setLabel('Category 1')->setColor('#aaaaaa');
        $cat2 = (new CategoryRequestBuilder())->setKey(2)->setLabel('Category 2')->setColor('#bbbbbb')->setAccessible(true);

        $chart = $this->seatsioClient->charts->create(null, null, [$cat1, $cat2]);

        self::assertNotEmpty($chart->name);
    }

}
