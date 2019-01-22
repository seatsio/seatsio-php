<?php

namespace Seatsio\Events;

class BestAvailableObjects
{
    /**
     * @var string[]
     */
    public $objects;
    /**
     * @var array[\Seatsio\Reports\EventReportItem]
     */
    public $objectDetails;
    /**
     * @var boolean
     */
    public $nextToEachOther;

}