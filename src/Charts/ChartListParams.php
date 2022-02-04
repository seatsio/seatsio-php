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
     * @var boolean
     */
    public $expandEvents;

    public function __construct(string $filter = null, string $tag = null, bool $expandEvents = null, bool $withValidation = false)
    {
        $this->filter = $filter;
        $this->tag = $tag;
        $this->expandEvents = $expandEvents;
        $this->validation = $withValidation;
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

    public function withValidation(bool $withValidation): self
    {
        $this->validation = $withValidation;
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

        if ($this->expandEvents) {
            $result["expand"] = "events";
        }

        if ($this->validation) {
            $result["validation"] = "true";
        }

        return $result;
    }
}
