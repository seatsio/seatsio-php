<?php

namespace Seatsio\Events;

class Object
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
    public function withTicketType($ticketType)
    {
        $this->ticketType = $ticketType;
        return $this;
    }

    /**
     * @param $quantity int
     * @return $this
     */
    public function withQuantity($quantity)
    {
        $this->quantity = $quantity;
        return $this;
    }

    /**
     * @param $extraData object|array
     * @return $this
     */
    public function withExtraData($extraData)
    {
        $this->extraData = $extraData;
        return $this;
    }
}