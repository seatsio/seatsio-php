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
}
