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
     * @var string
     */
    public $name;

    /**
     * @var \Seatsio\LocalDate
     */
    public $date;

    /**
     * @var bool
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

    /**
     * @var bool
     */
    public $isInThePast;

    /**
     * @var string[]
     */
    public $partialSeasonKeysForEvent;

    public function isSeason()
    {
        return false;
    }

}
