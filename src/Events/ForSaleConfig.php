<?php

namespace Seatsio\Events;

class ForSaleConfig
{
    /**
     * @var boolean
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

    public function __construct(bool $forSale, array $objects = null, array $areaPlaces = null, array $categories = null) {
        $this->forSale = $forSale;
        $this->objects = $objects;
        $this-> areaPlaces = $areaPlaces;
        $this->categories = $categories;
    }
}
