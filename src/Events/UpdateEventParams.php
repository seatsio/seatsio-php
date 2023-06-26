<?php

namespace Seatsio\Events;

class UpdateEventParams extends EventParams
{
    /**
     * @var string
     */
    public $chartKey;

    static function create(): UpdateEventParams
    {
        return new UpdateEventParams();
    }

    public function setChartKey(string $chartKey): self
    {
        $this->chartKey = $chartKey;
        return $this;
    }
}
