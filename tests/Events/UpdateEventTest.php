<?php

namespace Seatsio\Events;

use Seatsio\Charts\Category;
use Seatsio\LocalDate;
use Seatsio\SeatsioClientTest;
use stdClass;

class UpdateEventTest extends SeatsioClientTest
{

    public function testUpdateChartKey()
    {
        $chart1 = $this->seatsioClient->charts->create();
        $chart2 = $this->seatsioClient->charts->create();
        $event = $this->seatsioClient->events->create($chart1->key);

        $this->seatsioClient->events->update($event->key, UpdateEventParams::create()->setChartKey($chart2->key));

        $retrievedEvent = $this->seatsioClient->events->retrieve($event->key);
        self::assertEquals($chart2->key, $retrievedEvent->chartKey);
        self::assertNotNull($retrievedEvent->updatedOn);
    }

    public function testUpdateEventKey()
    {
        $chart = $this->seatsioClient->charts->create();
        $event = $this->seatsioClient->events->create($chart->key);

        $this->seatsioClient->events->update($event->key, UpdateEventParams::create()->setKey('newKey'));

        $retrievedEvent = $this->seatsioClient->events->retrieve('newKey');
        self::assertEquals($chart->key, $retrievedEvent->chartKey);
        self::assertEquals('newKey', $retrievedEvent->key);
    }

    public function testUpdateName()
    {
        $chart = $this->seatsioClient->charts->create();
        $event = $this->seatsioClient->events->create($chart->key);

        $this->seatsioClient->events->update($event->key, UpdateEventParams::create()->setName('My event'));

        $retrievedEvent = $this->seatsioClient->events->retrieve($event->key);
        self::assertEquals('My event', $retrievedEvent->name);
    }

    public function testUpdateDate()
    {
        $chart = $this->seatsioClient->charts->create();
        $event = $this->seatsioClient->events->create($chart->key);

        $this->seatsioClient->events->update($event->key, UpdateEventParams::create()->setDate(LocalDate::create(2020, 1, 5)));

        $retrievedEvent = $this->seatsioClient->events->retrieve($event->key);
        self::assertEquals(LocalDate::create(2020, 1, 5), $retrievedEvent->date);
    }

    public function testUpdateTableBookingConfig()
    {
        $chartKey = $this->createTestChartWithTables();
        $event = $this->seatsioClient->events->create($chartKey);

        $this->seatsioClient->events->update($event->key, UpdateEventParams::create()->setTableBookingConfig(TableBookingConfig::custom(["T1" => "BY_TABLE", "T2" => "BY_SEAT"])));

        $retrievedEvent = $this->seatsioClient->events->retrieve($event->key);
        self::assertEquals($chartKey, $retrievedEvent->chartKey);
        self::assertEquals($event->key, $retrievedEvent->key);
        self::assertEquals(TableBookingConfig::custom(["T1" => "BY_TABLE", "T2" => "BY_SEAT"]), $retrievedEvent->tableBookingConfig);
    }

    public function testUpdateObjectCategories()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey, CreateEventParams::create()->setObjectCategories(["A-1" => 9]));

        $this->seatsioClient->events->update($event->key, UpdateEventParams::create()->setObjectCategories(["A-2" => 10]));

        $retrievedEvent = $this->seatsioClient->events->retrieve($event->key);
        self::assertEquals(["A-2" => 10], $retrievedEvent->objectCategories);
    }

    public function testRemoveObjectCategories()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey, CreateEventParams::create()->setObjectCategories(["A-1" => 9]));

        $this->seatsioClient->events->update($event->key, UpdateEventParams::create()->setObjectCategories(new stdClass()));

        $retrievedEvent = $this->seatsioClient->events->retrieve($event->key);
        self::assertNull($retrievedEvent->objectCategories);
    }

    public function testUpdateCategories()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey);
        $eventCategories = [new Category("eventCategory", "event-level category", "#AAABBB")];

        $this->seatsioClient->events->update($event->key, UpdateEventParams::create()->setCategories($eventCategories));

        $retrievedEvent = $this->seatsioClient->events->retrieve($event->key);
        self::assertEquals(4, count($retrievedEvent->categories));
        $eventCategory = current(array_filter($retrievedEvent->categories, function ($category) {
            return $category->key == 'eventCategory';
        }));
        self::assertNotNull($eventCategory);
        self::assertEquals('eventCategory', $eventCategory->key);
        self::assertEquals('event-level category', $eventCategory->label);
        self::assertEquals('#AAABBB', $eventCategory->color);
        self::assertEquals(false, $eventCategory->accessible);

    }

    public function testRemoveCategories()
    {
        $chartKey = $this->createTestChart();
        $eventCategories = [new Category("eventCategory", "event-level category", "#AAABBB")];
        $event = $this->seatsioClient->events->create($chartKey, CreateEventParams::create()->setCategories($eventCategories));

        $this->seatsioClient->events->update($event->key, UpdateEventParams::create()->setCategories([]));

        $retrievedEvent = $this->seatsioClient->events->retrieve($event->key);
        self::assertEquals(3, count($retrievedEvent->categories));
        $eventCategory = current(array_filter($retrievedEvent->categories, function ($category) {
            return $category->key == 'eventCategory';
        }));
        self::assertFalse($eventCategory);
    }
}
