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

    public function __construct(string $name = null, string $color = null, int $index = null, string $key = null, array $objects = [])
    {
        $this->name = $name;
        $this->color = $color;
        $this->index = $index;
        $this->key = $key;
        $this->objects = $objects;
    }
}
