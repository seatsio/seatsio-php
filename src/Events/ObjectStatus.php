<?php

namespace Seatsio\Events;

class ObjectStatus
{
    public static $FREE = "free";
    public static $BOOKED = "booked";
    public static $HELD = "reservedByToken";

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