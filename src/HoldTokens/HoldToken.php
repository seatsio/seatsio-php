<?php

namespace Seatsio\HoldTokens;

class HoldToken
{

    /**
     * @var string
     */
    public $holdToken;
    /**
     * @var \DateTime
     */
    public $expiresAt;
    /**
     * @var int
     */
    public $expiresInSeconds;
    /**
     * @var int
     */
    public $accountId;

}
