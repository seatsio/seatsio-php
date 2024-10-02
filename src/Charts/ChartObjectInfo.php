<?php

namespace Seatsio\Charts;

class ChartObjectInfo
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
     * @var \Seatsio\Common\IDs
     */
    var $ids;
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
     * @var int
     */
    public $capacity;
    /**
     * @var bool
     */
    public $bookAsAWhole;
    /**
     * @var string
     */
    public $leftNeighbour;
    /**
     * @var string
     */
    public $rightNeighbour;
    /**
     * @var float
     */
    public $distanceToFocalPoint;
    /**
     * @var int
     */
    public $numSeats;
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
    public $zone;
    /**
     * @var \Seatsio\Common\Floor
     */
    var $floor;
}
