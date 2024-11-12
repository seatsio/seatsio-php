<?php

namespace Seatsio\Events;

class UpdateEventParams extends EventParams
{
    /**
     * @var bool
     */
    public $isInThePast;

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
}
