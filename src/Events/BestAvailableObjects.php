<?php

namespace Seatsio\Events;

use Seatsio\SeatsioJsonMapper;

class BestAvailableObjects
{
    /**
     * @var string[]
     */
    public $objects;
    /**
     * @var array
     */
    public $labels;
    /**
     * @var boolean
     */
    public $nextToEachOther;

    /**
     * @param $json
     * @return BestAvailableObjects
     */
    static function fromJson($json)
    {
        $mapper = SeatsioJsonMapper::create();
        $result = $mapper->map($json, new BestAvailableObjects());
        array_walk($result->labels, function ($labels, $id) use ($mapper, $result) {
            $result->labels[$id] = $mapper->map($labels, new Labels());
        });
        return $result;
    }
}