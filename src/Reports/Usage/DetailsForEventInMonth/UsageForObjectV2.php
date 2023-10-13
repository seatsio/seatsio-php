<?php


namespace Seatsio\Reports\Usage\DetailsForEventInMonth;


class UsageForObjectV2
{

    /**
     * @var string
     */
    public $object;

    /**
     * @var int
     */
    public $numUsedObjects;

    /**
     * @var array
     */
    public $usageByReason;

    public function __construct(string $object = null, int $numUsedObjects = null, array $usageByReason = null)
    {
        $this->object = $object;
        $this->numUsedObjects = $numUsedObjects;
        $this->usageByReason = $usageByReason;
    }
}
