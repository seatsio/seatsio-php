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
    public $bookWholeTables;
    /**
     * @var ForSaleConfig
     */
    public $forSaleConfig;
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
}