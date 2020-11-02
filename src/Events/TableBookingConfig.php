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

    public function __construct($mode, $tables)
    {
        $this->mode = $mode;
        $this->tables = $tables;
    }

    public static function inherit()
    {
        return new TableBookingConfig('INHERIT', null);
    }

    public static function allBySeat()
    {
        return new TableBookingConfig('ALL_BY_SEAT', null);
    }

    public static function allByTable()
    {
        return new TableBookingConfig('ALL_BY_TABLE', null);
    }

    public static function custom($tables)
    {
        return new TableBookingConfig('CUSTOM', $tables);
    }
}
