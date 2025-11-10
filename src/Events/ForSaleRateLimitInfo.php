<?php

namespace Seatsio\Events;

class ForSaleRateLimitInfo
{
    /**
     * @var int
     */
    var $rateLimitRemainingCalls;

    /**
     * @var \DateTime
     */
    public $rateLimitResetDate;
}