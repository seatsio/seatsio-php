<?php

namespace Seatsio\Charts;

use Seatsio\SeatsioClientTest;

class ManageCategoriesTest extends SeatsioClientTest
{

    public function testAddCategory()
    {
        $categories = [
            (object)['key' => 1, 'label' => 'Category 1', 'color' => '#aaaaaa', 'accessible' => true]
        ];
        $chart = $this->seatsioClient->charts->create('aChart', null, $categories);

        $category2 = (new CategoryRequestBuilder())->setKey(2)->setLabel('Category 2')->setColor('blue');
        $this->seatsioClient->charts->addCategory($chart->key, $category2);

        $retrievedChart = $this->seatsioClient->charts->retrievePublishedVersion($chart->key);
        self::assertEquals('aChart', $retrievedChart->name);
        self::assertEquals([
            (object)['key' => 1, 'label' => 'Category 1', 'color' => '#aaaaaa', 'accessible' => true],
            (object)['key' => 2, 'label' => 'Category 2', 'color' => 'blue', 'accessible' => false]
        ], $retrievedChart->categories->list);
    }

    public function testRemoveCategory()
    {
        $categories = [
            (object)['key' => 1, 'label' => 'Category 1', 'color' => '#aaaaaa', 'accessible' => true],
            (object)['key' => 'cat2', 'label' => 'Category 2', 'color' => '#bbbbbb', 'accessible' => false]
        ];
        $chart = $this->seatsioClient->charts->create('aChart', null, $categories);

        $this->seatsioClient->charts->removeCategory($chart->key, 1);

        $retrievedChart = $this->seatsioClient->charts->retrievePublishedVersion($chart->key);
        self::assertEquals('aChart', $retrievedChart->name);
        self::assertEquals([
            (object)['key' => 'cat2', 'label' => 'Category 2', 'color' => '#bbbbbb', 'accessible' => false]
        ], $retrievedChart->categories->list);
    }

    public function testListCategories()
    {
        $categories = [
            new Category(1, 'Category 1', '#aaaaaa', true),
            new Category('cat2', 'Category 2', '#bbbbbb', false)
        ];
        $chart = $this->seatsioClient->charts->create('aChart', null, $categories);

        $categoryList = $this->seatsioClient->charts->listCategories($chart->key);
        self::assertEquals($categories, $categoryList);
    }
}
