<?php

namespace Seatsio\Charts;

use stdClass;

class CategoryRequestBuilder
{
    /**
     * @var string
     */
    public $key;
    /**
     * @var string
     */
    public $label;
    /**
     * @var string
     */
    public $color;
    /**
     * @var bool
     */
    public $accessible;

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

    public function build()
    {
        $categoryRequest = new stdClass();
        if($this->key !== null) {
            $categoryRequest->key = $this->key;
        }
        if($this->label !== null) {
            $categoryRequest->label = $this->label;
        }
        if($this->color !== null) {
            $categoryRequest->color = $this->color;
        }
        if($this->accessible !== null) {
            $categoryRequest->accessible = $this->accessible;
        }
        return $categoryRequest;
    }
}
