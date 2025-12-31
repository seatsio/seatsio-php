<?php

namespace Seatsio\Charts;

class CategoryUpdateParams
{
    /**
     * @var string
     */
    public string $label;

    /**
     * @var string
     */
    public string $color;

    /**
     * @var bool
     */
    public bool $accessible;

    public function __construct(?string $label = null, string $color = null, bool $accessible = false)
    {
        $this->label = $label;
        $this->color = $color;
        $this->accessible = $accessible;
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
