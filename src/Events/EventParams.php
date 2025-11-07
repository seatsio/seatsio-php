<?php

namespace Seatsio\Events;

abstract class EventParams
{
    /**
     * @var string
     */
    public $eventKey;

    /**
     * @var string
     */
    public $name;

    /**
     * @var bool|object|array
     */
    public $tableBookingConfig;

    /**
     * @var array
     */
    public $objectCategories;

    /**
     * @var array
     */
    public $categories;

    public function setKey(string $key): self
    {
        $this->eventKey = $key;
        return $this;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function setTableBookingConfig(TableBookingConfig $tableBookingConfig): self
    {
        $this->tableBookingConfig = $tableBookingConfig;
        return $this;
    }

    public function setObjectCategories($objectCategories): self
    {
        $this->objectCategories = $objectCategories;
        return $this;
    }

    /**
     * @param \Seatsio\Charts\Category[] $categories
     */
    public function setCategories(array $categories): self
    {
        $this->categories = $categories;
        return $this;
    }
}
