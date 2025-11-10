<?php

namespace Seatsio\Events;

use Seatsio\LocalDate;

class UpdateEventParams extends EventParams
{
    /**
     * @var bool
     */
    public $isInThePast;

    /**
     * @var \Seatsio\LocalDate
     */
    public $date;

    static function create(): UpdateEventParams
    {
        return new UpdateEventParams();
    }

    public function setChartKey(string $chartKey): self
    {
        $this->chartKey = $chartKey;
        return $this;
    }

    public function setIsInThePast(bool $isInThePast) {
        $this->isInThePast = $isInThePast;
        return $this;
    }

    public function setDate(LocalDate $date): self
    {
        $this->date = $date;
        return $this;
    }
}
