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