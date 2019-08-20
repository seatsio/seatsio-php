<?php

namespace Seatsio\Charts;

class Category
{

    /**
     * @param $key int|string
     * @return $this
     */
    public function setKey($key)
    {
        $this->key = $key;
        return $this;
    }

    /**
     * @param $label string
     * @return $this
     */
    public function setLabel($label)
    {
        $this->label = $label;
        return $this;
    }

    /**
     * @param $color string
     * @return $this
     */
    public function setColor($color)
    {
        $this->color = $color;
        return $this;
    }
}
