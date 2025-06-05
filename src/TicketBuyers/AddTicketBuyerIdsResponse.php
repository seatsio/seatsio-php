<?php

namespace Seatsio\TicketBuyers;

class AddTicketBuyerIdsResponse {
    /**
     * @var string[]
     */
    public $added;

    /**
     * @var string[]
     */
    public $alreadyPresent;

    public function __construct() {
        $this->added = [];
        $this->alreadyPresent = [];
    }
}
