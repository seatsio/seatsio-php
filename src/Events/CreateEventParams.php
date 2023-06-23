<?php

namespace Seatsio\Events;

class CreateEventParams extends EventParams
{
    static function create(): CreateEventParams
    {
        return new CreateEventParams();
    }
}
