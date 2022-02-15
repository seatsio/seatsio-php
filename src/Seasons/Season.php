<?php

namespace Seatsio\Seasons;

use Seatsio\Events\Event;

class Season extends Event
{
    /**
     * @var \Seatsio\Events\Event[]
     */
    public $events;

    /**
     * @var string[]
     */
    public $partialSeasonKeys;

    public function isSeason() {
        return true;
    }
}
