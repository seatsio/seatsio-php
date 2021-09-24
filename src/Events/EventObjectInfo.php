<?php

namespace Seatsio\Events;

class EventObjectInfo
{
    public static $HELD = "reservedByToken";
    public static $BOOKED = "booked";
    public static $FREE = "free";
    /**
     * @var string
     */
    public $label;
    /**
     * @var \Seatsio\Common\Labels
     */
    var $labels;
    /**
     * @var \Seatsio\Common\IDs
     */
    var $ids;
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
     * @var bool
     */
    public $bookAsAWhole;
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
    /**
     * @var float
     */
    public $distanceToFocalPoint;
    /**
     * @var object
     */
    public $holds;

}
