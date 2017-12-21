<?php

namespace Seatsio\Events;

class StatusChange
{
    public $id;
    public $eventId;
    public $status;
    public $quantity;
    public $objectLabelOrUuid;
    public $date;
    public $orderId;
    public $extraData;
}