<?php

use Seatsio\Common\Floor;

function aFloor($name, $displayName)
{
    $floor = new Floor();
    $floor->name = $name;
    $floor->displayName = $displayName;
    return $floor;
}
