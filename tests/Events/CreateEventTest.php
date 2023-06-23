<?php

namespace Seatsio\Events;

use Seatsio\Charts\Category;
use Seatsio\Charts\SocialDistancingRuleset;
use Seatsio\LocalDate;
use Seatsio\SeatsioClientTest;

class CreateEventTest extends SeatsioClientTest
{

    public function testOnlyChartKeyIsRequired()
    {
        $chartKey = $this->createTestChart();

        $event = $this->seatsioClient->events->create($chartKey);

        self::assertNotNull($event->key);
        self::assertNotNull($event->id);
        self::assertNull($event->name);
        self::assertNull($event->date);
        self::assertEquals($chartKey, $event->chartKey);
        self::assertEquals(TableBookingConfig::inherit(), $event->tableBookingConfig);
        self::assertTrue($event->supportsBestAvailable);
        self::assertNotNull($event->createdOn);
        self::assertNull($event->forSaleConfig);
        self::assertNull($event->updatedOn);
    }

    public function testEventKeyCanBePassedIn()
    {
        $chartKey = $this->createTestChart();

        $event = $this->seatsioClient->events->create($chartKey, CreateEventParams::create()->setKey('eventje'));

        self::assertEquals('eventje', $event->key);
    }

    public function testTableBookingConfigCustomCanBePassedIn()
    {
        $chartKey = $this->createTestChartWithTables();

        $event = $this->seatsioClient->events->create($chartKey, CreateEventParams::create()->setTableBookingConfig(TableBookingConfig::custom(["T1" => "BY_TABLE", "T2" => "BY_SEAT"])));

        self::assertNotNull($event->key);
        self::assertEquals(TableBookingConfig::custom(["T1" => "BY_TABLE", "T2" => "BY_SEAT"]), $event->tableBookingConfig);
    }

    public function testTableBookingConfigInheritCanBePassedIn()
    {
        $chartKey = $this->createTestChartWithTables();

        $event = $this->seatsioClient->events->create($chartKey, CreateEventParams::create()->setTableBookingConfig(TableBookingConfig::inherit()));

        self::assertNotNull($event->key);
        self::assertEquals(TableBookingConfig::inherit(), $event->tableBookingConfig);
    }

    public function testSocialDistancingRulesetKeyCanBePassedIn()
    {
        $chartKey = $this->createTestChartWithTables();
        $this->seatsioClient->charts->saveSocialDistancingRulesets($chartKey, ["ruleset1" => SocialDistancingRuleset::ruleBased("My ruleset")->build()]);

        $event = $this->seatsioClient->events->create($chartKey, CreateEventParams::create()->setSocialDistancingRulesetKey("ruleset1"));

        self::assertEquals("ruleset1", $event->socialDistancingRulesetKey);
    }

    public function testObjectCategoriesCanBePassedIn()
    {
        $chartKey = $this->createTestChart();

        $event = $this->seatsioClient->events->create($chartKey, CreateEventParams::create()->setObjectCategories(["A-1" => 10]));

        self::assertEquals(["A-1" => 10], $event->objectCategories);
    }

    public function testCategoriesCanBePassedIn()
    {
        $chartKey = $this->createTestChart();

        $category = new Category("eventCategory", "event-level category", "#AAABBB");
        $categories = [
            $category
        ];
        $event = $this->seatsioClient->events->create($chartKey, CreateEventParams::create()->setCategories($categories));

        self::assertEquals(4, count($event->categories));
        $eventCategory = current(array_filter($event->categories, function ($category) {
            return $category->key == 'eventCategory';
        }));
        self::assertNotNull($eventCategory);
        self::assertEquals('eventCategory', $eventCategory->key);
        self::assertEquals('event-level category', $eventCategory->label);
        self::assertEquals('#AAABBB', $eventCategory->color);
        self::assertEquals(false, $eventCategory->accessible);
    }

    public function testNameCanBePassedIn()
    {
        $chartKey = $this->createTestChart();

        $event = $this->seatsioClient->events->create($chartKey, CreateEventParams::create()->setName('My event'));

        self::assertEquals('My event', $event->name);
    }

    public function testDateCanBePassedIn()
    {
        $chartKey = $this->createTestChart();

        $event = $this->seatsioClient->events->create($chartKey, CreateEventParams::create()->setDate(LocalDate::create(2022, 1, 5)));

        self::assertEquals(LocalDate::create(2022, 1, 5), $event->date);
    }
}
