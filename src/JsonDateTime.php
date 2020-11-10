<?php

namespace Seatsio;

use DateTime;
use DateTimeZone;

class JsonDateTime extends DateTime
{
    public function __construct($dateString)
    {
        parent::__construct($dateString, new DateTimeZone("UTC"));
    }
}
