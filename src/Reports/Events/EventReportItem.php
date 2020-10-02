<?php

namespace Seatsio\Reports\Events;

class EventReportItem
{
    /**
     * @var string
     */
    public $label;
    /**
     * @var \Seatsio\Common\Labels
     */
    var $labels;
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
    /**
     * @var int
     */
    public $numFree;
    /**
     * @var int
     */
    public $numHeld;
    /**
     * @var object
     */
    public $extraData;
    /**
     * @var boolean
     */
    public $isAccessible;
    /**
     * @var boolean
     */
    public $isCompanionSeat;
    /**
     * @var boolean
     */
    public $hasRestrictedView;
    /**
     * @var string
     */
    public $displayedObjectType;
    /**
     * @var string
     */
    public $leftNeighbour;
    /**
     * @var string
     */
    public $rightNeighbour;
    /**
     * @var boolean
     */
    public $isSelectable;
    /**
     * @var boolean
     */
    public $isDisabledBySocialDistancing;
    /**
     * @var string
     */
    public $channel;

}
