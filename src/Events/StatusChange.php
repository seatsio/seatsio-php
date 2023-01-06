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
    public $objectLabel;
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
    /**
     * @var StatusChangeOrigin
     */
    public $origin;
    /**
     * @var boolean
     */
    public $isPresentOnChart;
    /**
     * @var string
     */
    public $notPresentOnChartReason;
    /**
     * @var string
     */
    public $holdToken;
}
