<?php

namespace Seatsio\Charts;


class ChartListParams
{

    /**
     * @var string
     */
    public $filter;

    /**
     * @var string
     */
    public $tag;

    /**
     * @var bool
     */
    public $expandEvents;

    /**
     * @var bool
     */
    public $expandValidation;

    /**
     * @var bool
     */
    public $expandVenueType;

    /**
     * @var bool
     */
    public $expandZones;

    public function __construct(?string $filter = null, ?string $tag = null, bool $expandEvents = false, bool $withExpandValidation = false, bool $withExpandVenueType = false, bool $withExpandZones = false)
    {
        $this->filter = $filter;
        $this->tag = $tag;
        $this->expandEvents = $expandEvents;
        $this->expandValidation = $withExpandValidation;
        $this->expandVenueType = $withExpandVenueType;
        $this->expandZones = $withExpandZones;
    }

    public function withFilter(string $filter): self
    {
        $this->filter = $filter;
        return $this;
    }

    public function withTag(string $tag): self
    {
        $this->tag = $tag;
        return $this;
    }

    public function withExpandEvents(bool $expandEvents): self
    {
        $this->expandEvents = $expandEvents;
        return $this;
    }

    public function withExpandValidation(bool $expandValidation): self
    {
        $this->expandValidation = $expandValidation;
        return $this;
    }

    /**
     * @deprecated Use withExpandValidation instead
     */
    public function withValidation(bool $withValidation): self
    {
        return $this->withExpandValidation($withValidation);
    }

    public function withExpandVenueType(bool $expandVenueType): self
    {
        $this->expandVenueType = $expandVenueType;
        return $this;
    }

    public function withExpandZones(bool $expandZones): self
    {
        $this->expandZones = $expandZones;
        return $this;
    }

    public function toArray(): array
    {
        $result = [];

        if ($this->filter != null) {
            $result["filter"] = $this->filter;
        }

        if ($this->tag != null) {
            $result["tag"] = $this->tag;
        }

        $result["expand"] = $this->expandParams();

        return $result;
    }

    private function expandParams()
    {
        $result = [];

        if ($this->expandEvents) {
            $result[] = "events";
        }

        if ($this->expandValidation) {
            $result[] = "validation";
        }

        if ($this->expandVenueType) {
            $result[] = "venueType";
        }

        if ($this->expandZones) {
            $result[] = "zones";
        }

        return $result;
    }
}
