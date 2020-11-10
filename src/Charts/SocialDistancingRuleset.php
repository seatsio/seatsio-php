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
     * @var int
     */
    public $maxOccupancyAbsolute;

    /**
     * @var int
     */
    public $maxOccupancyPercentage;

    /**
     * @var boolean
     */
    public $oneGroupPerTable;

    /**
     * @var boolean
     */
    public $fixedGroupLayout;

    /**
     * @var string[]
     */
    public $disabledSeats;

    /**
     * @var string[]
     */
    public $enabledSeats;

    public function __construct($index = 0, $name = null, $numberOfDisabledSeatsToTheSides = 0, $disableSeatsInFrontAndBehind = false, $numberOfDisabledAisleSeats = 0, $maxGroupSize = 0,
     $maxOccupancyAbsolute = 0, $maxOccupancyPercentage = 0, $oneGroupPerTable = false, $fixedGroupLayout = false, $disabledSeats = [], $enabledSeats = [])
    {
        $this->index = $index;
        $this->name = $name;
        $this->numberOfDisabledSeatsToTheSides = $numberOfDisabledSeatsToTheSides;
        $this->disableSeatsInFrontAndBehind = $disableSeatsInFrontAndBehind;
        $this->numberOfDisabledAisleSeats = $numberOfDisabledAisleSeats;
        $this->maxGroupSize = $maxGroupSize;
        $this->maxOccupancyAbsolute = $maxOccupancyAbsolute;
        $this->maxOccupancyPercentage = $maxOccupancyPercentage;
        $this->oneGroupPerTable = $oneGroupPerTable;
        $this->fixedGroupLayout = $fixedGroupLayout;
        $this->disabledSeats = $disabledSeats;
        $this->enabledSeats = $enabledSeats;
    }

    public static function fixed($index = 0, $name = null, $disabledSeats = []) {
        return new SocialDistancingRuleset($index, $name, 0, false, 0, 0, 0, 0, false, true, $disabledSeats, []);
    }

    public static function ruleBased($index = 0, $name = null, $numberOfDisabledSeatsToTheSides = 0, $disableSeatsInFrontAndBehind = false, $numberOfDisabledAisleSeats = 0,
    $maxGroupSize = 0, $maxOccupancyAbsolute = 0, $maxOccupancyPercentage = 0, $oneGroupPerTable = false, $disabledSeats = [], $enabledSeats = []) {
        return new SocialDistancingRuleset($index, $name, $numberOfDisabledSeatsToTheSides, $disableSeatsInFrontAndBehind, $numberOfDisabledAisleSeats, $maxGroupSize,
            $maxOccupancyAbsolute, $maxOccupancyPercentage, $oneGroupPerTable, false, $disabledSeats, $enabledSeats);
    }
}
