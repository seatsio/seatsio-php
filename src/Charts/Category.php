<?php

namespace Seatsio\Charts;

class Category
{
    /**
     * @var string
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

    /**
     * @param string $key
     * @param string $label
     * @param string $color
     * @param bool $accessible
     */
    public function __construct(string $key, string $label, string $color, bool $accessible = false)
    {
        $this->key = $key;
        $this->label = $label;
        $this->color = $color;
        $this->accessible = $accessible;
    }


}
