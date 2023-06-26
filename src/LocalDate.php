<?php

namespace Seatsio;

class LocalDate
{
    /**
     * @var int
     */
    public $year;

    /**
     * @var int
     */
    public $month;

    /**
     * @var int
     */
    public int $day;

    public function __construct(string $date = null)
    {
        if ($date != null) {
            $this->year = (int)substr($date, 0, 4);
            $this->month = (int)substr($date, 5, 2);
            $this->day = (int)substr($date, 8, 2);
        }
    }

    public static function create(int $year, int $month, int $day)
    {
        $localDate = new LocalDate();
        $localDate->year = $year;
        $localDate->month = $month;
        $localDate->day = $day;
        return $localDate;
    }

    public function serialize(): string
    {
        return $this->year . '-' . str_pad($this->month, 2, '0', STR_PAD_LEFT) . '-' . str_pad($this->day, 2, '0', STR_PAD_LEFT);
    }
}
