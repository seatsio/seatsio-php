<?php

namespace Seatsio\Events;

class Labels
{
    /**
     * @var LabelAndType
     */
    public $own;

    /**
     * @var LabelAndType
     */
    public $parent;

    /**
     * @var string
     */
    public $section;

    /**
     * @var Entrance
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