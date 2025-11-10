<?php

namespace Seatsio\Events;

use stdClass;

class ForSaleConfig
{
    /**
     * @var bool
     */
    public $forSale;
    /**
     * @var string[]
     */
    public $objects;
    /**
     * @var array
     */
    public $areaPlaces;
    /**
     * @var string[]
     */
    public $categories;

    public function __construct(bool $forSale, array $objects = [], array $areaPlaces = null, array $categories = [])
    {
        $this->forSale = $forSale;
        $this->objects = $objects;
        $this->areaPlaces = $areaPlaces == null ? new stdClass() : $areaPlaces;
        $this->categories = $categories;
    }
}
