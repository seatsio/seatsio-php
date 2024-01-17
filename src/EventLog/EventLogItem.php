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
    public $date;

    /**
     * @var string
     */
    public $workspaceKey;

    /**
     * @var object
     */
    public $data;
}
