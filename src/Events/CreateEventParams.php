<?php

namespace Seatsio\Events;

class CreateEventParams extends EventParams
{
    /**
     * @var \Seatsio\Events\Channel[]
     */
    public $channels;

    /**
     * @var \Seatsio\Events\ForSaleConfig
     */
    public $forSaleConfig;

    static function create(): CreateEventParams
    {
        return new CreateEventParams();
    }

    /**
     * @param \Seatsio\Events\Channel[] $channels
     */
    public function setChannels(array $channels): self
    {
        $this->channels = $channels;
        return $this;
    }

    /**
     * @param \Seatsio\Events\ForSaleConfig $forSaleConfig
     */
    public function setForSaleConfig(ForSaleConfig $forSaleConfig): self
    {
        $this->forSaleConfig = $forSaleConfig;
        return $this;
    }
}
