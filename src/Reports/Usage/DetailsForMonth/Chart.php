<?php


namespace Seatsio\Reports\Usage\DetailsForMonth;


class Chart
{

    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $key;

    public function __construct(string $name = null, string $key = null)
    {
        $this->name = $name;
        $this->key = $key;
    }
}
