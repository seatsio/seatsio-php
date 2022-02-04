<?php

namespace Seatsio;

use JsonMapper;

class SeatsioJsonMapper extends JsonMapper
{
    public static function create(): SeatsioJsonMapper
    {
        $mapper = new SeatsioJsonMapper();
        $mapper->classMap["\DateTime"] = JsonDateTime::class;
        return $mapper;
    }

    protected function getSafeName($name): string
    {
        // avoid object labels such as B-4 being converted into B4
        return $name;
    }
}
