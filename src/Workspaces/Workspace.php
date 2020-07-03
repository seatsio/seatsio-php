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
     * @var boolean
     */
    public $isTest;

    /**
     * @var boolean
     */
    public $isActive;

}
