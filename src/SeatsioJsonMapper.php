<?php

namespace Seatsio;

use JsonMapper;

class SeatsioJsonMapper extends JsonMapper
{
    public static function create(): SeatsioJsonMapper
    {
        $eventMapper = function ($class, $json) {
            if ($json->isSeason) {
                return "\Seatsio\Seasons\Season";
            }
            return "\Seatsio\Events\Event";
        };

        $mapper = new SeatsioJsonMapper();
        $mapper->classMap["\DateTime"] = JsonDateTime::class;
        $mapper->classMap["Seatsio\Events\Event"] = $eventMapper;
        $mapper->bStrictObjectTypeChecking = false;
        return $mapper;
    }

    protected function getSafeName($name): string
    {
        // avoid object labels such as B-4 being converted into B4
        return $name;
    }
}
