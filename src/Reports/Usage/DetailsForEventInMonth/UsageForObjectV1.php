<?php


namespace Seatsio\Reports\Usage\DetailsForEventInMonth;


class UsageForObjectV1
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
