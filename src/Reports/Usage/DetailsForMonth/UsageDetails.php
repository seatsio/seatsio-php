<?php


namespace Seatsio\Reports\Usage\DetailsForMonth;


class UsageDetails
{

    /**
     * @var int
     */
    public $workspace;

    /**
     * @var UsageByChart[]
     */
    public $usageByChart;

    public function __construct(int $workspace = null, array $usageByChart = null)
    {
        $this->workspace = $workspace;
        $this->usageByChart = $usageByChart;
    }
}
