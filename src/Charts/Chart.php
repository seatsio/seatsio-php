<?php

namespace Seatsio\Charts;

class Chart
{
    /**
     * @var string
     */
    public $name;
    /**
     * @var int
     */
    public $id;
    /**
     * @var string
     */
    public $key;
    /**
     * @var string
     */
    public $status;
    /**
     * @var string[]
     */
    public $tags;
    /**
     * @var string
     */
    public $publishedVersionThumbnailUrl;
    /**
     * @var string
     */
    public $draftVersionThumbnailUrl;
    /**
     * @var \Seatsio\Events\Event[]
     */
    public $events;
    /**
     * @var bool
     */
    public $archived;
    /**
     * @var array
     */
    public $validation;
    /**
     * @var string
     */
    public $venueType;
    /**
     * @var \Seatsio\Charts\Zone[]
     */
    public $zones;
}
