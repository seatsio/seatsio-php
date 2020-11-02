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
     * @var ForSaleConfig
     */
    public $forSaleConfig;

    /**
     * @var TableBookingConfig
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
     * @var Channel[]
     */
    public $channels;

    /**
     * @var string
     */
    public $socialDistancingRulesetKey;
}
