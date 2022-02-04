<?php

namespace Seatsio\Charts;

class SocialDistancingRuleset
{
    /**
     * @var int
     */
    public $index = 0;

    /**
     * @var string
     */
    public $name;

    /**
     * @var int
     */
    public $numberOfDisabledSeatsToTheSides = 0;

    /**
     * @var boolean
     */
    public $disableSeatsInFrontAndBehind = false;

    /**
     * @var boolean
     */
    public $disableDiagonalSeatsInFrontAndBehind = false;

    /**
     * @var int
     */
    public $numberOfDisabledAisleSeats = 0;

    /**
     * @var int
     */
    public $maxGroupSize = 0;

    /**
     * @var int
     */
    public $maxOccupancyAbsolute = 0;

    /**
     * @var int
     */
    public $maxOccupancyPercentage = 0;

    /**
     * @var boolean
     */
    public $oneGroupPerTable = false;

    /**
     * @var boolean
     */
    public $fixedGroupLayout = false;

    /**
     * @var string[]
     */
    public $disabledSeats = [];

    /**
     * @var string[]
     */
    public $enabledSeats = [];

    public static function fixed($name): FixedSocialDistancingRulesetBuilder
    {
        return new FixedSocialDistancingRulesetBuilder($name);
    }

    public static function ruleBased($name): RuleBasedSocialDistancingRulesetBuilder
    {
        return new RuleBasedSocialDistancingRulesetBuilder($name);
    }
}

class FixedSocialDistancingRulesetBuilder
{

    /**
     * @var int
     */
    public $index = 0;

    /**
     * @var string
     */
    public $name;

    /**
     * @var string[]
     */
    public $disabledSeats = [];

    public function __construct($name)
    {
        $this->name = $name;
    }

    public function build(): SocialDistancingRuleset
    {
        $ruleset = new SocialDistancingRuleset();
        $ruleset->index = $this->index;
        $ruleset->name = $this->name;
        $ruleset->fixedGroupLayout = true;
        $ruleset->disabledSeats = $this->disabledSeats;
        return $ruleset;
    }

    public function setIndex($index): self
    {
        $this->index = $index;
        return $this;
    }

    public function setDisabledSeats($disabledSeats): self
    {
        $this->disabledSeats = $disabledSeats;
        return $this;
    }

}


class RuleBasedSocialDistancingRulesetBuilder
{

    /**
     * @var int
     */
    public $index = 0;

    /**
     * @var string
     */
    public $name;

    /**
     * @var int
     */
    public $numberOfDisabledSeatsToTheSides = 0;

    /**
     * @var boolean
     */
    public $disableSeatsInFrontAndBehind = false;

    /**
     * @var boolean
     */
    public $disableDiagonalSeatsInFrontAndBehind = false;

    /**
     * @var int
     */
    public $numberOfDisabledAisleSeats = 0;

    /**
     * @var int
     */
    public $maxGroupSize = 0;

    /**
     * @var int
     */
    public $maxOccupancyAbsolute = 0;

    /**
     * @var int
     */
    public $maxOccupancyPercentage = 0;

    /**
     * @var boolean
     */
    public $oneGroupPerTable = false;

    /**
     * @var string[]
     */
    public $disabledSeats = [];

    /**
     * @var string[]
     */
    public $enabledSeats = [];

    public function __construct($name)
    {
        $this->name = $name;
    }

    public function build(): SocialDistancingRuleset
    {
        $ruleset = new SocialDistancingRuleset();
        $ruleset->index = $this->index;
        $ruleset->name = $this->name;
        $ruleset->numberOfDisabledSeatsToTheSides = $this->numberOfDisabledSeatsToTheSides;
        $ruleset->disableSeatsInFrontAndBehind = $this->disableSeatsInFrontAndBehind;
        $ruleset->disableDiagonalSeatsInFrontAndBehind = $this->disableDiagonalSeatsInFrontAndBehind;
        $ruleset->numberOfDisabledAisleSeats = $this->numberOfDisabledAisleSeats;
        $ruleset->maxGroupSize = $this->maxGroupSize;
        $ruleset->maxOccupancyAbsolute = $this->maxOccupancyAbsolute;
        $ruleset->maxOccupancyPercentage = $this->maxOccupancyPercentage;
        $ruleset->oneGroupPerTable = $this->oneGroupPerTable;
        $ruleset->fixedGroupLayout = false;
        $ruleset->disabledSeats = $this->disabledSeats;
        $ruleset->enabledSeats = $this->enabledSeats;
        return $ruleset;
    }

    public function setIndex(int $index): self
    {
        $this->index = $index;
        return $this;
    }

    public function setNumberOfDisabledSeatsToTheSides(int $numberOfDisabledSeatsToTheSides): self
    {
        $this->numberOfDisabledSeatsToTheSides = $numberOfDisabledSeatsToTheSides;
        return $this;
    }

    public function setDisableSeatsInFrontAndBehind(bool $disableSeatsInFrontAndBehind): self
    {
        $this->disableSeatsInFrontAndBehind = $disableSeatsInFrontAndBehind;
        return $this;
    }

    public function setDisableDiagonalSeatsInFrontAndBehind(bool $disableDiagonalSeatsInFrontAndBehind): self
    {
        $this->disableDiagonalSeatsInFrontAndBehind = $disableDiagonalSeatsInFrontAndBehind;
        return $this;
    }

    public function setNumberOfDisabledAisleSeats(int $numberOfDisabledAisleSeats): self
    {
        $this->numberOfDisabledAisleSeats = $numberOfDisabledAisleSeats;
        return $this;
    }

    public function setMaxGroupSize(int $maxGroupSize): self
    {
        $this->maxGroupSize = $maxGroupSize;
        return $this;
    }

    public function setMaxOccupancyAbsolute(int $maxOccupancyAbsolute): self
    {
        $this->maxOccupancyAbsolute = $maxOccupancyAbsolute;
        return $this;
    }

    public function setMaxOccupancyPercentage(int $maxOccupancyPercentage): self
    {
        $this->maxOccupancyPercentage = $maxOccupancyPercentage;
        return $this;
    }

    public function setOneGroupPerTable(bool $oneGroupPerTable): self
    {
        $this->oneGroupPerTable = $oneGroupPerTable;
        return $this;
    }

    public function setDisabledSeats(array $disabledSeats): self
    {
        $this->disabledSeats = $disabledSeats;
        return $this;
    }

    public function setEnabledSeats(array $enabledSeats): self
    {
        $this->enabledSeats = $enabledSeats;
        return $this;
    }
}
