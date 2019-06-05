<?php


namespace Seatsio\Reports\Usage\DetailsForEventInMonth;


class UsageForObject
{

    /**
     * @var string
     */
    public $object;

    /**
     * @var int
     */
    public $numFirstBookings;

    /**
     * @var \DateTime
     */
    public $firstBookingDate;

    /**
     * @var int
     */
    public $numFirstSelections;

    /**
     * @var int
     */
    public $numFirstBookingsOrSelections;
}
