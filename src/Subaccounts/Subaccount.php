<?php

namespace Seatsio\Subaccounts;

class Subaccount
{
    /**
     * @var int
     */
    public $id;
    /**
     * @var int
     */
    public $accountId;
    /**
     * @var string
     */
    public $secretKey;
    /**
     * @var string
     */
    public $designerKey;
    /**
     * @var string
     */
    public $publicKey;
    /**
     * @var string
     */
    public $name;
    /**
     * @var string
     */
    public $email;
    /**
     * @var boolean
     */
    public $active;

}
