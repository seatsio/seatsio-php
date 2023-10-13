<?php


namespace Seatsio\Reports\Usage\DetailsForMonth;


class UsageByChart
{

    /**
     * @var Chart
     */
    public $chart;

    /**
     * @var UsageByEvent[]
     */
    public $usageByEvent;

    public function __construct(Chart $chart = null, array $usageByEvent = null)
    {
        $this->chart = $chart;
        $this->usageByEvent = $usageByEvent;
    }
}
