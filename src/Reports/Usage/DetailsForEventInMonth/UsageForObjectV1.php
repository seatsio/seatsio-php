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

    public function __construct(string $object = null, int $numFirstBookings = null, \DateTime $firstBookingDate = null, int $numFirstSelections = null, int $numFirstBookingsOrSelections = null)
    {
        $this->object = $object;
        $this->numFirstBookings = $numFirstBookings;
        $this->firstBookingDate = $firstBookingDate;
        $this->numFirstSelections = $numFirstSelections;
        $this->numFirstBookingsOrSelections = $numFirstBookingsOrSelections;
    }
}
