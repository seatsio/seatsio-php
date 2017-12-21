<?php

namespace Seatsio\Events;

class StatusChange
{
    /**
     * @var int
     */
    public $id;
    /**
     * @var int
     */
    public $eventId;
    /**
     * @var string
     */
    public $status;
    /**
     * @var int
     */
    public $quantity;
    /**
     * @var string
     */
    public $objectLabelOrUuid;
    /**
     * @var \DateTime
     */
    public $date;
    /**
     * @var string
     */
    public $orderId;
    /**
     * @var object
     */
    public $extraData;
}