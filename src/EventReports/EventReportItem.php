<?php

namespace Seatsio\EventReports;


class EventReportItem
{
    /**
     * @var string
     */
    public $label;
    /**
     * @var string
     */
    public $status;
    /**
     * @var string
     */
    public $categoryLabel;
    /**
     * @var string
     */
    public $categoryKey;
    /**
     * @var string
     */
    public $ticketType;
    /**
     * @var string
     */
    public $entrance;
    /**
     * @var string
     */
    public $objectType;
    /**
     * @var string
     */
    public $section;
    /**
     * @var string
     */
    public $orderId;
    /**
     * @var boolean
     */
    public $forSale;
    /**
     * @var string
     */
    public $holdToken;
    /**
     * @var int
     */
    public $capacity;
    /**
     * @var int
     */
    public $numBooked;
}