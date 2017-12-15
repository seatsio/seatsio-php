<?php

namespace Seatsio;


class ChartPage extends Page
{
    public $items;

    /**
     * @param Chart[] $items
     */
    public function setItems($items)
    {
        $this->items = $items;
    }

}