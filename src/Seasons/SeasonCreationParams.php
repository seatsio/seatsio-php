<?php

namespace Seatsio\Seasons;

use Seatsio\Events\TableBookingConfig;

class SeasonCreationParams
{

    /**
     * @var string
     */
    public $key;

    /**
     * @var string[]
     */
    public $eventKeys;

    /**
     * @var int
     */
    public $numberOfEvents;

    /**
     * @var boolean|object|array
     */
    public $tableBookingConfig;

    /**
     * @var string
     */
    public $socialDistancingRulesetKey;

    public function __construct($key = null)
    {
        $this->key = $key;
    }

    static function seasonCreationParams($key = null)
    {
        return new SeasonCreationParams($key);
    }

    /**
     * @param $eventKeys string[]
     * @return SeasonCreationParams
     */
    public function setEventKeys($eventKeys)
    {
        $this->eventKeys = $eventKeys;
        return $this;
    }

    /**
     * @param $numberOfEvents int
     * @return SeasonCreationParams
     */
    public function setNumberOfEvents($numberOfEvents)
    {
        $this->numberOfEvents = $numberOfEvents;
        return $this;
    }

    /**
     * @param $tableBookingConfig TableBookingConfig
     * @return SeasonCreationParams
     */
    public function setTableBookingConfig($tableBookingConfig)
    {
        $this->tableBookingConfig = $tableBookingConfig;
        return $this;
    }

    /**
     * @param $socialDistancingRulesetKey string
     * @return SeasonCreationParams
     */
    public function setSocialDistancingRulesetKey($socialDistancingRulesetKey)
    {
        $this->socialDistancingRulesetKey = $socialDistancingRulesetKey;
        return $this;
    }

}
