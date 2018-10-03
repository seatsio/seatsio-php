<?php

namespace Seatsio;

use JsonMapper;

class SeatsioJsonMapper extends JsonMapper
{
    /**
     * @return JsonMapper
     */
    public static function create()
    {
        $mapper = new SeatsioJsonMapper();
        $mapper->classMap["\DateTime"] = JsonDateTime::class;
        return $mapper;
    }

    protected function getSafeName($name)
    {
        // avoid object labels such as B-4 being converted into B4
        return $name;
    }
}