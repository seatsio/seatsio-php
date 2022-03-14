<?php

namespace Seatsio\Charts;

class Category
{
    /**
     * @var string|int
     */
    public $key;
    /**
     * @var string
     */
    public $label;
    /**
     * @var string
     */
    public $color;
    /**
     * @var bool
     */
    public $accessible;


    public function setKey($key): self
    {
        $this->key = $key;
        return $this;
    }

    public function setLabel($label): self
    {
        $this->label = $label;
        return $this;
    }

    public function setColor($color): self
    {
        $this->color = $color;
        return $this;
    }

    public function setAccessible($accessible): self
    {
        $this->accessible = $accessible;
        return $this;
    }
}
