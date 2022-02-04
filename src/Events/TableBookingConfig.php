<?php

namespace Seatsio\Events;

class TableBookingConfig
{
    /**
     * @var string
     */
    public $mode;

    /**
     * @var array
     */
    public $tables;

    public function __construct(string $mode, ?array $tables)
    {
        $this->mode = $mode;
        $this->tables = $tables;
    }

    public static function inherit(): TableBookingConfig
    {
        return new TableBookingConfig('INHERIT', null);
    }

    public static function allBySeat(): TableBookingConfig
    {
        return new TableBookingConfig('ALL_BY_SEAT', null);
    }

    public static function allByTable(): TableBookingConfig
    {
        return new TableBookingConfig('ALL_BY_TABLE', null);
    }

    public static function custom(array $tables): TableBookingConfig
    {
        return new TableBookingConfig('CUSTOM', $tables);
    }
}
