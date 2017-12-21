<?php

namespace Seatsio\Events;

class SeatsioObject
{
    public $objectId;

    public function __construct($objectId)
    {
        $this->objectId = $objectId;
    }

    /**
     * @param $ticketType string
     * @return $this
     */
    public function setTicketType($ticketType)
    {
        $this->ticketType = $ticketType;
        return $this;
    }

    /**
     * @param $quantity int
     * @return $this
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;
        return $this;
    }

    /**
     * @param $extraData object|array
     * @return $this
     */
    public function setExtraData($extraData)
    {
        $this->extraData = $extraData;
        return $this;
    }
}