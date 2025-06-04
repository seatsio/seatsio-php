<?php

namespace Seatsio\TicketBuyers;

class RemoveTicketBuyerIdsResponse
{
    /**
     * @var string[]
     */
    public $removed;

    /**
     * @var string[]
     */
    public $notPresent;

    public function __construct(array $removed = [], array $notPresent = [])
    {
        $this->removed = $removed;
        $this->notPresent = $notPresent;
    }
}
