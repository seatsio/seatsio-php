<?php

namespace Seatsio\Events;

class Event
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var string
     */
    public $key;

    /**
     * @var boolean
     */
    public $supportsBestAvailable;

    /**
     * @var \Seatsio\Events\ForSaleConfig
     */
    public $forSaleConfig;

    /**
     * @var \Seatsio\Events\TableBookingConfig
     */
    public $tableBookingConfig;

    /**
     * @var string
     */
    public $chartKey;

    /**
     * @var \DateTime
     */
    public $createdOn;

    /**
     * @var \DateTime
     */
    public $updatedOn;

    /**
     * @var \Seatsio\Events\Channel[]
     */
    public $channels;

    /**
     * @var string
     */
    public $socialDistancingRulesetKey;

    /**
     * @var string
     */
    public $topLevelSeasonKey;

    /**
     * @var bool
     */
    public $isTopLevelSeason;

    /**
     * @var bool
     */
    public $isPartialSeason;

    /**
     * @var bool
     */
    public $isEventInSeason;

    public function isSeason() {
        return false;
    }
}
