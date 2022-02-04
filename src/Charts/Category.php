<?php

namespace Seatsio\Charts;

class Category
{

    /**
     * @param $key int|string
     */
    public function setKey($key): self
    {
        $this->key = $key;
        return $this;
    }

    public function setLabel(string $label): self
    {
        $this->label = $label;
        return $this;
    }

    public function setColor(string $color): self
    {
        $this->color = $color;
        return $this;
    }

    public function setAccessible(bool $accessible): self
    {
        $this->accessible = $accessible;
        return $this;
    }
}
