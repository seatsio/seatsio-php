<?php

namespace Seatsio\Events;

class Channel
{
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

    public function __construct(?string $key = null, ?string $name = null, ?string $color = null, ?int $index = null, array $objects = [], ?array $areaPlaces = [])
    {
        $this->key = $key;
        $this->name = $name;
        $this->color = $color;
        $this->index = $index;
        $this->objects = $objects;
        $this->areaPlaces = $areaPlaces ?? [];
    }

    public function toArray(): array
    {
        return [
            'key' => $this->key,
            'name' => $this->name,
            'color' => $this->color,
            'index' => $this->index,
            'objects' => $this->objects,
            'areaPlaces' => !empty($this->areaPlaces) ? $this->areaPlaces : new \stdClass(),
        ];
    }
}
