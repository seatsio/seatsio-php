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

    public function __construct(string $key = null, string $name = null, string $color = null, int $index = null, array $objects = [])
    {
        $this->key = $key;
        $this->name = $name;
        $this->color = $color;
        $this->index = $index;
        $this->objects = $objects;
    }
}
