<?php


namespace Seatsio\Reports\Usage\DetailsForMonth;


class UsageByEvent
{

    /**
     * @var Event
     */
    public $event;

    /**
     * @var int
     */
    public $numUsedObjects;

    /**
     * @var int
     */
    public $numFirstBookings;

    /**
     * @var int
     */
    public $numFirstBookingsOrSelections;

    /**
     * @var int
     */
    public $numGASelectionsWithoutBooking;

    /**
     * @var int
     */
    public $numNonGASelectionsWithoutBooking;

    /**
     * @var int
     */
    public $numObjectSelections;
}
