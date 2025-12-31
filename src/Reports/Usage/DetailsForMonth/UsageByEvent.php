<?php


namespace Seatsio\Reports\Usage\DetailsForMonth;


class UsageByEvent
{

    /**
     * @var Event
     */
    public $event;

    /**
     * @var int
     */
    public $numUsedObjects;

    /**
     * @param Event $event
     * @param int $numUsedObjects
     */
    public function __construct(?Event $event = null, ?int $numUsedObjects = null)
    {
        $this->event = $event;
        $this->numUsedObjects = $numUsedObjects;
    }
}
