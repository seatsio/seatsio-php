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

    public function __construct(string $eventKey = null)
    {
        $this->eventKey = $eventKey;
    }

    public function setTableBookingConfig(TableBookingConfig $tableBookingConfig): self
    {
        $this->tableBookingConfig = $tableBookingConfig;
        return $this;
    }

    public function setSocialDistancingRulesetKey(string $socialDistancingRulesetKey): self
    {
        $this->socialDistancingRulesetKey = $socialDistancingRulesetKey;
        return $this;
    }

}
