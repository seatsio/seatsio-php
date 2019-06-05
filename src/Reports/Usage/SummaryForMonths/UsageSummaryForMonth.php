<?php


namespace Seatsio\Reports\Usage\SummaryForMonths;


class UsageSummaryForMonth
{

    /**
     * @var Month
     */
    public $month;

    /**
     * @var int
     */
    public $numUsedObjects;

    /**
     * @var int
     */
    public $numFirstBookings;

    /**
     * @var object
     */
    public $numFirstBookingsByStatus;

    /**
     * @var int
     */
    public $numFirstBookingsOrSelections;

}
