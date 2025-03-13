<?php

namespace Seatsio\Events;

class BestAvailableParams
{
    /**
     * @var int
     */
    public $number;

    /**
     * @var string[]
     */
    public $categories;

    /**
     * @var string
     */
    public $zone;

    /**
     * @var string[]
     */
    public $sections;

    /**
     * @var array
     */
    public $extraData;

    /**
     * @var string[]
     */
    public $ticketTypes;

    /**
     * @var bool
     */
    public $tryToPreventOrphanSeats;

    /**
     * @var int
     */
    public $accessibleSeats;

    public function setNumber(int $number): self
    {
        $this->number = $number;
        return $this;
    }

    public function setCategories(array $categories): self
    {
        $this->categories = $categories;
        return $this;
    }

    public function setZone(string $zone): self
    {
        $this->zone = $zone;
        return $this;
    }

    public function setSections(array $sections): self
    {
        $this->sections = $sections;
        return $this;
    }


    public function setExtraData(array $extraData): self
    {
        $this->extraData = $extraData;
        return $this;
    }

    public function setTicketTypes(array $ticketTypes): self
    {
        $this->ticketTypes = $ticketTypes;
        return $this;
    }

    public function setTryToPreventOrphanSeats(bool $tryToPreventOrphanSeats): self
    {
        $this->tryToPreventOrphanSeats = $tryToPreventOrphanSeats;
        return $this;
    }

    public function setAccessibleSeats(int $accessibleSeats): self
    {
        $this->accessibleSeats = $accessibleSeats;
        return $this;
    }
}
