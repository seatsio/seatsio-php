<?php

namespace Seatsio\Charts;

class SocialDistancingRuleset
{
    /**
     * @var int
     */
    public $index;

    /**
     * @var string
     */
    public $name;

    /**
     * @var int
     */
    public $numberOfDisabledSeatsToTheSides;

    /**
     * @var boolean
     */
    public $disableSeatsInFrontAndBehind;

    /**
     * @var int
     */
    public $numberOfDisabledAisleSeats;

    /**
     * @var int
     */
    public $maxGroupSize;

    /**
     * @var array[string]
     */
    public $disabledSeats;

    /**
     * @var array[string]
     */
    public $enabledSeats;

    public function __construct($index = 0, $name = null, $numberOfDisabledSeatsToTheSides = 0, $disableSeatsInFrontAndBehind = false, $numberOfDisabledAisleSeats = 0, $maxGroupSize = 0, $disabledSeats = [], $enabledSeats = [])
    {
        $this->index = $index;
        $this->name = $name;
        $this->numberOfDisabledSeatsToTheSides = $numberOfDisabledSeatsToTheSides;
        $this->disableSeatsInFrontAndBehind = $disableSeatsInFrontAndBehind;
        $this->numberOfDisabledAisleSeats = $numberOfDisabledAisleSeats;
        $this->maxGroupSize = $maxGroupSize;
        $this->disabledSeats = $disabledSeats;
        $this->enabledSeats = $enabledSeats;
    }
}
