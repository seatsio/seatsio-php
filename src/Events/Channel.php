<?php

namespace Seatsio\Events;

class Channel
{
    /**
     * @var string
     */
    public $key;

    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $color;

    /**
     * @var int
     */
    public $index;

    /**
     * @var array
     */
    public $objects;

    public function __construct($name, $color, $index, $key = null, $objects = [])
    {
        $this->name = $name;
        $this->color = $color;
        $this->index = $index;
        $this->key = $key;
        $this->objects = $objects;
    }
}
