<?php

namespace Seatsio\Accounts;

class Account
{
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
    public $email;
    /**
     * @var AccountSettings
     */
    public $settings;
}