<?php

namespace Seatsio\Events;

class Channel
{
    /**
     * @var string
     */
    public $id;

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

    /**
     * @var array
     */
    public $areaPlaces;

    public function __construct(string $key, string $id, string $name, string $color, int $index, array $objects, array $areaPlaces)
    {
        $this->key = $key;
        $this->id = $id;
        $this->name = $name;
        $this->color = $color;
        $this->index = $index;
        $this->objects = $objects;
        $this->setAreaPlaces($areaPlaces);
    }

    public function setAreaPlaces($areaPlaces): void
    {
        $this->areaPlaces = is_array($areaPlaces) ? $areaPlaces : (array)$areaPlaces;
    }

    public function areaPartitionLabel(string $areaLabel): string
    {
        return $areaLabel . '##' . $this->id;
    }


}
