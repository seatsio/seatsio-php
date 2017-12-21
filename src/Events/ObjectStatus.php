<?php

namespace Seatsio\Events;

class ObjectStatus
{
    /**
     * @var string
     */
    public $status;
    /**
     * @var string
     */
    public $ticketType;
    /**
     * @var string
     */
    public $holdToken;
    /**
     * @var string
     */
    public $orderId;
    /**
     * @var object
     */
    public $extraData;
    /**
     * @var int
     */
    public $quantity;
}