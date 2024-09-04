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
     * @var \Seatsio\Common\Floor
     */
    var $floor;
    /**
     * @var string
     */
    public $orderId;
    /**
     * @var bool
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
     * @var int
     */
    public $numNotForSale;
    /**
     * @var object
     */
    public $extraData;
    /**
     * @var bool
     */
    public $isAccessible;
    /**
     * @var bool
     */
    public $isCompanionSeat;
    /**
     * @var bool
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
     * @var bool
     */
    public $isAvailable;
    /**
     * @var string
     */
    public $availabilityReason;
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
    /**
     * @var int
     */
    public $numSeats;
    /**
     * @var bool
     */
    public $variableOccupancy;
    /**
     * @var int
     */
    public $minOccupancy;
    /**
     * @var int
     */
    public $maxOccupancy;
    /**
     * @var int
     */
    public $seasonStatusOverriddenQuantity;
    /**
     * @var string
     */
    public $zone;
}
