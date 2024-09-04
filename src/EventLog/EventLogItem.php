<?php

namespace Seatsio\EventLog;

class EventLogItem
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var string
     */
    public $type;

    /**
     * @var \Seatsio\LocalDate
     */
    public $timestamp;

    /**
     * @var object
     */
    public $data;
}
