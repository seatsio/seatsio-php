<?php

namespace Seatsio\Events;

use Seatsio\LocalDate;

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
     * @var string
     */
    public $name;

    /**
     * @var \Seatsio\LocalDate
     */
    public $date;

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

    /**
     * @var array
     */
    public $objectCategories;

    /**
     * @var \Seatsio\Charts\Category[]
     */
    public $categories;

    public function isSeason()
    {
        return false;
    }

}
