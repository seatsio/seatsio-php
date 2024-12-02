<?php

namespace Seatsio\Events;

class ObjectProperties
{
    /**
     * @var string
     */
    public $objectId;
    /**
     * @var string
     */
    public $ticketType;
    /**
     * @var int
     */
    public $quantity;
    /**
     * @var array
     */
    public $extraData;


    public function __construct(string $objectId)
    {
        $this->objectId = $objectId;
    }

    public function setTicketType(string $ticketType): self
    {
        $this->ticketType = $ticketType;
        return $this;
    }

    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;
        return $this;
    }

    public function setExtraData(array $extraData): self
    {
        $this->extraData = $extraData;
        return $this;
    }
}
