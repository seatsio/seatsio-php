<?php

namespace Seatsio\Charts;

class Zone
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
     * @param string $key
     * @param string $label
     */
    public function __construct(string $key, string $label)
    {
        $this->key = $key;
        $this->label = $label;
    }


}
