<?php


namespace Seatsio\Reports\Usage\DetailsForMonth;


class Event
{

    /**
     * @var int
     */
    public $id;

    /**
     * @var string
     */
    public $key;

    /**
     * @var bool
     */
    public $deleted;

    public function __construct(int $id = null, string $key = null, bool $deleted = null)
    {
        $this->id = $id;
        $this->key = $key;
        $this->deleted = $deleted;
    }
}
