<?php

namespace Seatsio\Seasons;

class Season
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var string
     */
    public $key;

    /**
     * @var \Seatsio\Events\Event
     */
    public $seasonEvent;

    /**
     * @var \Seatsio\Events\Event[]
     */
    public $events;

    /**
     * @var string[]
     */
    public $partialSeasonKeys;
}
