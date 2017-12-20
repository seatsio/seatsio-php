<?php

namespace Seatsio\Events;

class Event
{
    public $id;
    public $key;
    public $bookWholeTables;
    /**
     * @var ForSaleConfig
     */
    public $forSaleConfig;
    public $chartKey;
    public $createdOn;
    public $updatedOn;
}