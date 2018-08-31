<?php

namespace Seatsio\Events;

class Labels
{
    /**
     * @var LabelAndType
     */
    public $own;

    /**
     * @var LabelAndType|null
     */
    public $parent;

    /**
     * @var string|null
     */
    public $section;

    /**
     * @var Entrance|null
     */
    public $entrance;

}

class LabelAndType
{

    /**
     * @var string
     */
    public $type;

    /**
     * @var string
     */
    public $label;

}

class Entrance
{
    /**
     * @var string
     */
    public $label;
}