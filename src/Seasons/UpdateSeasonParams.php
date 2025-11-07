<?php

namespace Seatsio\Seasons;

use Seatsio\Events\EventParams;
use Seatsio\LocalDate;

class UpdateSeasonParams extends EventParams
{
    /**
     * @var bool
     */
    public $forSalePropagated;

    static function create(): UpdateSeasonParams
    {
        return new UpdateSeasonParams();
    }

    public function setForSalePropagated(bool $forSalePropagated): self
    {
        $this->forSalePropagated = $forSalePropagated;
        return $this;
    }
}
