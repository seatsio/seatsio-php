<?php

namespace Seatsio\Workspaces;

class Workspace
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $key;

    /**
     * @var string
     */
    public $secretKey;

    /**
     * @var bool
     */
    public $isTest;

    /**
     * @var bool
     */
    public $isDefault;

    /**
     * @var bool
     */
    public $isActive;

}
