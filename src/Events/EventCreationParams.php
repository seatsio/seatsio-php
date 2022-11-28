<?php

namespace Seatsio\Events;

use Exception;

class EventCreationParams
{

    /**
     * @var string
     */
    public $eventKey;

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

    public function __construct(string $eventKey = null)
    {
        $this->eventKey = $eventKey;
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
