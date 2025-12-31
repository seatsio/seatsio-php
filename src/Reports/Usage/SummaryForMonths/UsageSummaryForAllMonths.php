<?php


namespace Seatsio\Reports\Usage\SummaryForMonths;


class UsageSummaryForAllMonths
{

    /**
     * @var \DateTime
     */
    public $usageCutoffDate;

    /**
     * @var UsageSummaryForMonth[]
     */
    public $usage;

    /**
     * @param \DateTime $usageCutoffDate
     * @param UsageSummaryForMonth[] $usage
     */
    public function __construct(?\DateTime $usageCutoffDate = null, ?array $usage = null)
    {
        $this->usageCutoffDate = $usageCutoffDate;
        $this->usage = $usage;
    }
}
