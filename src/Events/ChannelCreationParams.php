<?php

namespace Seatsio\Events;

class ChannelCreationParams
{

    /**
     * @var string
     */
    public string $channelKey;

    /**
     * @var string
     */
    public string $name;

    /**
     * @var string
     */
    public string $color;

    /**
     * @var int
     */
    public int $index;

    /**
     * @var array
     */
    public array $objects;

    public function setChannelKey(string $channelKey): self
    {
        $this->channelKey = $channelKey;
        return $this;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function setColor(string $color): self
    {
        $this->color = $color;
        return $this;
    }

    public function setIndex(int $index): self
    {
        $this->index = $index;
        return $this;
    }

    public function setObjects(array $objects): self
    {
        $this->objects = $objects;
        return $this;
    }
}
