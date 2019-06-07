<?php

namespace Seatsio\Reports;

use Seatsio\Reports\Usage\SummaryForMonths\Month;
use Seatsio\SeatsioClientTest;

class UsageReportsTest extends SeatsioClientTest
{

    public function testSummaryForAllMonths()
    {
        $report = $this->seatsioClient->usageReports->summaryForAllMonths();
    }

    public function testDetailsForMonth()
    {
        $report = $this->seatsioClient->usageReports->detailsForMonth(new Month(2019, 5));
    }

    public function testDetailsForEventInMonth()
    {
        $chart = $this->seatsioClient->charts->create();
        $event = $this->seatsioClient->events->create($chart->key);
        $report = $this->seatsioClient->usageReports->detailsForEventInMonth($event->id, new Month(2019, 5));
    }

}
