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

    public function __construct($eventKey = null)
    {
        $this->eventKey = $eventKey;
    }

    /**
     * @param $tableBookingConfig TableBookingConfig
     * @return EventCreationParams
     * @throws Exception
     */
    public function setTableBookingConfig($tableBookingConfig)
    {
        $this->tableBookingConfig = $tableBookingConfig;
        return $this;
    }

    /**
     * @param $socialDistancingRulesetKey string
     * @return EventCreationParams
     */
    public function setSocialDistancingRulesetKey($socialDistancingRulesetKey)
    {
        $this->socialDistancingRulesetKey = $socialDistancingRulesetKey;
        return $this;
    }

}
