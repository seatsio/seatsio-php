<?php

namespace Seatsio\Common;

class IDs
{
    /**
     * @var string
     */
    public $own;

    /**
     * @var string
     */
    public $parent;

    /**
     * @var string
     */
    public $section;

    public function __construct($own, $parent, $section)
    {
        $this->own = $own;
        $this->parent = $parent;
        $this->section = $section;
    }
}
