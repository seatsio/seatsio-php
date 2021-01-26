<?php

namespace Seatsio\Reports\Charts;

class ChartReportItem
{
    /**
     * @var string
     */
    public $label;
    /**
     * @var \Seatsio\Common\Labels
     */
    var $labels;
    /**
     * @var string
     */
    public $categoryLabel;
    /**
     * @var string
     */
    public $categoryKey;
    /**
     * @var string
     */
    public $entrance;
    /**
     * @var string
     */
    public $objectType;
    /**
     * @var string
     */
    public $section;
    /**
     * @var int
     */
    public $capacity;
    /**
     * @var bool
     */
    public $bookAsAWhole;
    /**
     * @var string
     */
    public $leftNeighbour;
    /**
     * @var string
     */
    public $rightNeighbour;
}
