<?php

namespace Seatsio\Events;

use Composer\DependencyResolver\LocalRepoTransaction;
use Seatsio\Charts\Category;
use Seatsio\LocalDate;
use Seatsio\SeatsioClientTest;

class CreateEventsTest extends SeatsioClientTest
{

    public function test_multipleEvents()
    {
        $chartKey = $this->createTestChart();
        $params = [CreateEventParams::create(), CreateEventParams::create()];

        $events = $this->seatsioClient->events->createMultiple($chartKey, $params);

        self::assertEquals(2, sizeof($events));
        foreach ($events as $e) {
            self::assertNotNull($e->key);
        }
    }

    public function test_single_event()
    {
        $chartKey = $this->createTestChart();
        $params = [CreateEventParams::create()];

        $events = $this->seatsioClient->events->createMultiple($chartKey, $params);

        self::assertEquals(1, sizeof($events));
        $event = $events[0];
        self::assertNotEquals(0, $event->id);
        self::assertNotNull($event->id);
        self::assertNotNull($event->key);
        self::assertEquals($chartKey, $event->chartKey);
        self::assertEquals(TableBookingConfig::inherit(), $event->tableBookingConfig);
        self::assertEquals(true, $event->supportsBestAvailable);
        self::assertEquals(3, count($event->categories));
        self::assertEquals(TableBookingConfig::inherit(), $event->tableBookingConfig);
        self::assertNull($event->forSaleConfig);
        self::assertNull($event->updatedOn);
    }

    public function test_event_key_can_be_passed_in()
    {
        $chartKey = $this->createTestChart();
        $params = [CreateEventParams::create()->setKey("event1"), CreateEventParams::create()->setKey("event2")];

        $events = $this->seatsioClient->events->createMultiple($chartKey, $params);

        self::assertEquals(2, sizeof($events));
        self::assertEquals("event1", $events[0]->key);
        self::assertEquals("event2", $events[1]->key);
    }

    public function test_tableBookingConfigCanBePassedIn()
    {
        $chartKey = $this->createTestChartWithTables();
        $params = [
            CreateEventParams::create()->setTableBookingConfig(TableBookingConfig::custom(["T1" => "BY_TABLE", "T2" => "BY_SEAT"])),
            CreateEventParams::create()->setTableBookingConfig(TableBookingConfig::custom(["T1" => "BY_SEAT", "T2" => "BY_TABLE"]))
        ];

        $events = $this->seatsioClient->events->createMultiple($chartKey, $params);

        self::assertEquals(2, sizeof($events));
        self::assertEquals(TableBookingConfig::custom(["T1" => "BY_TABLE", "T2" => "BY_SEAT"]), $events[0]->tableBookingConfig);
        self::assertEquals(TableBookingConfig::custom(["T1" => "BY_SEAT", "T2" => "BY_TABLE"]), $events[1]->tableBookingConfig);
    }

    public function test_objectCategoriesCanBePassedIn()
    {
        $chartKey = $this->createTestChart();

        $params = [CreateEventParams::create()->setObjectCategories(["A-1" => 10])];
        $events = $this->seatsioClient->events->createMultiple($chartKey, $params);

        self::assertEquals(["A-1" => 10], $events[0]->objectCategories);
    }

    public function test_categoriesCanBePassedIn()
    {
        $chartKey = $this->createTestChart();
        $params = [
            CreateEventParams::create()->setCategories([
                new Category("eventCategory", "event-level category", "#AAABBB")
            ])
        ];
        $events = $this->seatsioClient->events->createMultiple($chartKey, $params);

        self::assertEquals(4, count($events[0]->categories));
        $eventCategory = current(array_filter($events[0]->categories, function ($category) {
            return $category->key == 'eventCategory';
        }));
        self::assertNotNull($eventCategory);
        self::assertEquals('eventCategory', $eventCategory->key);
        self::assertEquals('event-level category', $eventCategory->label);
        self::assertEquals('#AAABBB', $eventCategory->color);
        self::assertEquals(false, $eventCategory->accessible);
    }

    public function test_nameCanBePassedIn()
    {
        $chartKey = $this->createTestChart();

        $params = [CreateEventParams::create()->setName('My event')];
        $events = $this->seatsioClient->events->createMultiple($chartKey, $params);

        self::assertEquals('My event', $events[0]->name);
    }

    public function test_dateCanBePassedIn()
    {
        $chartKey = $this->createTestChart();

        $params = [CreateEventParams::create()->setDate(LocalDate::create(2022, 1, 10))];
        $events = $this->seatsioClient->events->createMultiple($chartKey, $params);

        self::assertEquals(LocalDate::create(2022, 1, 10), $events[0]->date);
    }

    public function test_channelsCanBePassedIn()
    {
        $chartKey = $this->createTestChart();
        $channels = [
            new Channel("channelKey1", "channel 1", "#FF0000", 1, ["A-1", "A-2"]),
            new Channel("channelKey2", "channel 2", "#00FFFF", 2, [])
        ];

        $params = [CreateEventParams::create()->setChannels($channels)];
        $events = $this->seatsioClient->events->createMultiple($chartKey, $params);

        self::assertEquals($channels, $events[0]->channels);
    }

}
