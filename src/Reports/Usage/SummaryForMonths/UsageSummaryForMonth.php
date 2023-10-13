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
     * @param Month $month
     * @param int $numUsedObjects
     */
    public function __construct(Month $month = null, int $numUsedObjects = null)
    {
        $this->month = $month;
        $this->numUsedObjects = $numUsedObjects;
    }
}
