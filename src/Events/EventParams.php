<?php

namespace Seatsio\Events;

use Seatsio\LocalDate;

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
     * @var \Seatsio\LocalDate
     */
    public $date;

    /**
     * @var boolean|object|array
     */
    public $tableBookingConfig;

    /**
     * @var string
     */
    public $socialDistancingRulesetKey;

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

    public function setDate(LocalDate $date): self
    {
        $this->date = $date;
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

    public function setCategories($categories): self
    {
        $this->categories = $categories;
        return $this;
    }

    public function setSocialDistancingRulesetKey(string $socialDistancingRulesetKey): self
    {
        $this->socialDistancingRulesetKey = $socialDistancingRulesetKey;
        return $this;
    }
}
