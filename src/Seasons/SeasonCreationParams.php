<?php

namespace Seatsio\Seasons;

use Seatsio\Events\ForSaleConfig;
use Seatsio\Events\TableBookingConfig;

class SeasonCreationParams
{

    /**
     * @var string
     */
    public $key;

    /**
     * @var string
     */
    public $name;

    /**
     * @var string[]
     */
    public $eventKeys;

    /**
     * @var int
     */
    public $numberOfEvents;

    /**
     * @var bool|object|array
     */
    public $tableBookingConfig;

    /**
     * @var \Seatsio\Events\Channel[]
     */
    public $channels;

    /**
     * @var \Seatsio\Events\ForSaleConfig
     */
    public $forSaleConfig;

    public function __construct(string $key = null)
    {
        $this->key = $key;
    }

    static function seasonCreationParams(string $key = null): SeasonCreationParams
    {
        return new SeasonCreationParams($key);
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
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

    /**
     * @param \Seatsio\Events\ForSaleConfig $forSaleConfig
     */
    public function setForSaleConfig(ForSaleConfig $forSaleConfig): self
    {
        $this->forSaleConfig = $forSaleConfig;
        return $this;
    }
}
