<?php

namespace Seatsio\Seasons;

use Seatsio\Events\TableBookingConfig;

class SeasonCreationParams
{

    /**
     * @var string
     */
    public $key;

    /**
     * @var string[]
     */
    public $eventKeys;

    /**
     * @var int
     */
    public $numberOfEvents;

    /**
     * @var boolean|object|array
     */
    public $tableBookingConfig;

    /**
     * @var \Seatsio\Events\Channel[]
     */
    public $channels;

    public function __construct(string $key = null)
    {
        $this->key = $key;
    }

    static function seasonCreationParams(string $key = null): SeasonCreationParams
    {
        return new SeasonCreationParams($key);
    }

    public function setEventKeys(array $eventKeys): self
    {
        $this->eventKeys = $eventKeys;
        return $this;
    }

    public function setNumberOfEvents(int $numberOfEvents): self
    {
        $this->numberOfEvents = $numberOfEvents;
        return $this;
    }

    public function setTableBookingConfig(TableBookingConfig $tableBookingConfig): self
    {
        $this->tableBookingConfig = $tableBookingConfig;
        return $this;
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
