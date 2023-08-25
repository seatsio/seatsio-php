<?php

namespace Seatsio\Events;

class CreateEventParams extends EventParams
{
    /**
     * @var \Seatsio\Events\Channel[]
     */
    public $channels;

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
}
