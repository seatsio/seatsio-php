<?php


namespace Seatsio\Reports\Usage\SummaryForMonths;


class Month
{

    /**
     * @var int
     */
    public $month;

    /**
     * @var int
     */
    public $year;

    public function __construct(int $year = null, int $month = null)
    {
        $this->year = $year;
        $this->month = $month;
    }

    public function serialize(): string
    {
        return $this->year . '-' . str_pad($this->month, 2, '0', STR_PAD_LEFT);
    }
}
