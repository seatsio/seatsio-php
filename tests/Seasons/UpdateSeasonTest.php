<?php

namespace Seasons;

use Seatsio\Charts\Category;
use Seatsio\Events\TableBookingConfig;
use Seatsio\Seasons\SeasonCreationParams;
use Seatsio\Seasons\UpdateSeasonParams;
use Seatsio\SeatsioClientTest;

class UpdateSeasonTest extends SeatsioClientTest
{
    public function testUpdateSeasonKey()
    {
        $chart = $this->seatsioClient->charts->create();
        $season = $this->seatsioClient->seasons->create($chart->key);

        $this->seatsioClient->seasons->update($season->key, UpdateSeasonParams::create()->setKey('newKey'));

        $retrievedSeason = $this->seatsioClient->seasons->retrieve('newKey');
        self::assertEquals($chart->key, $retrievedSeason->chartKey);
        self::assertEquals('newKey', $retrievedSeason->key);
    }

    public function testUpdateName()
    {
        $chart = $this->seatsioClient->charts->create();
        $season = $this->seatsioClient->seasons->create($chart->key);

        $this->seatsioClient->seasons->update($season->key, UpdateSeasonParams::create()->setName('My season'));

        $retrievedSeason = $this->seatsioClient->seasons->retrieve($season->key);
        self::assertEquals('My season', $retrievedSeason->name);
    }

    public function testUpdateTableBookingConfig()
    {
        $chartKey = $this->createTestChartWithTables();
        $season = $this->seatsioClient->seasons->create($chartKey);

        $this->seatsioClient->seasons->update($season->key, UpdateSeasonParams::create()->setTableBookingConfig(TableBookingConfig::custom(["T1" => "BY_TABLE", "T2" => "BY_SEAT"])));

        $retrievedSeason = $this->seatsioClient->seasons->retrieve($season->key);
        self::assertEquals($chartKey, $retrievedSeason->chartKey);
        self::assertEquals($season->key, $retrievedSeason->key);
        self::assertEquals(TableBookingConfig::custom(["T1" => "BY_TABLE", "T2" => "BY_SEAT"]), $retrievedSeason->tableBookingConfig);
    }

    public function testUpdateObjectCategories()
    {
        $chartKey = $this->createTestChart();
        $season = $this->seatsioClient->seasons->create($chartKey, (new SeasonCreationParams())->setObjectCategories(["A-1" => 9]));

        $this->seatsioClient->seasons->update($season->key, UpdateSeasonParams::create()->setObjectCategories(["A-2" => 10]));

        $retrievedSeason = $this->seatsioClient->seasons->retrieve($season->key);
        self::assertEquals(["A-2" => 10], $retrievedSeason->objectCategories);
    }

    public function testUpdateCategories()
    {
        $chartKey = $this->createTestChart();
        $season = $this->seatsioClient->seasons->create($chartKey);
        $categories = [new Category("eventCategory", "event-level category", "#AAABBB")];

        $this->seatsioClient->seasons->update($season->key, UpdateSeasonParams::create()->setCategories($categories));

        $retrievedSeason = $this->seatsioClient->seasons->retrieve($season->key);
        self::assertEquals(4, count($retrievedSeason->categories));
        $category = current(array_filter($retrievedSeason->categories, function ($category) {
            return $category->key == 'eventCategory';
        }));
        self::assertNotNull($category);
        self::assertEquals('eventCategory', $category->key);
        self::assertEquals('event-level category', $category->label);
        self::assertEquals('#AAABBB', $category->color);
        self::assertEquals(false, $category->accessible);
    }

    public function testUpdateForSalePropagatedFlag()
    {
        $chartKey = $this->createTestChart();
        $season = $this->seatsioClient->seasons->create($chartKey);

        $this->seatsioClient->seasons->update($season->key, UpdateSeasonParams::create()->setForSalePropagated(false));

        $retrievedSeason = $this->seatsioClient->seasons->retrieve($season->key);
        self::assertFalse($retrievedSeason->forSalePropagated);
    }
}
