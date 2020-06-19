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
    public $bookWholeTablesOrTableBookingModes;

    /**
     * @var string
     */
    public $socialDistancingRulesetKey;

    public function __construct($eventKey = null)
    {
        $this->eventKey = $eventKey;
    }

    public function setBookWholeTablesOrTableBookingModes($bookWholeTablesOrTableBookingModes)
    {
        $this->bookWholeTablesOrTableBookingModes = $bookWholeTablesOrTableBookingModes;
        return $this;
    }

    /**
     * @param $bookWholeTables bool
     * @return EventCreationParams
     * @throws Exception
     */
    public function setBookWholeTables($bookWholeTables)
    {
        if (is_bool($bookWholeTables)) {
            return $this->setBookWholeTablesOrTableBookingModes($bookWholeTables);
        } else {
            throw new Exception('expected bool param');
        }
    }

    /**
     * @param $tableBookingModes
     * @return EventCreationParams
     * @throws Exception
     */
    public function setTableBookingModes($tableBookingModes)
    {
        if (is_array($tableBookingModes)) {
            return $this->setBookWholeTablesOrTableBookingModes($tableBookingModes);
        } else {
            throw new Exception('expected array param');
        }
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
