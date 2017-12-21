<?php

namespace Seatsio;

use JsonMapper;

class SeatsioJsonMapper
{
    /**
     * @return JsonMapper
     */
    public static function create()
    {
        $mapper = new JsonMapper();
        $mapper->classMap["\DateTime"] = JsonDateTime::class;
        return $mapper;
    }
}