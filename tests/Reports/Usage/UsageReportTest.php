<?php

namespace Reports\Usage;

use Seatsio\Reports\Usage\DetailsForEventInMonth\UsageForObjectV1;
use Seatsio\Reports\Usage\DetailsForMonth\Event;
use Seatsio\Reports\Usage\DetailsForMonth\UsageByEvent;
use Seatsio\Reports\Usage\SummaryForMonths\Month;
use Seatsio\SeatsioClientTest;

class UsageReportTest extends SeatsioClientTest
{
    public function testUsageReportForAllMonths()
    {
        $this->skipTestIfDemoCompanySecretKeyNotSet();
        $client = $this->createSeatsioClient($this->demoCompanySecretKey());

        $report = $client->usageReports->summaryForAllMonths();

        self::assertNotNull($report->usageCutoffDate);
        self::assertGreaterThan(0, count($report->usage));
        self::assertEquals(new Month(2014, 2), $report->usage[0]->month);
    }

    public function testUsageReportForMonth()
    {
        $this->skipTestIfDemoCompanySecretKeyNotSet();
        $client = $this->createSeatsioClient($this->demoCompanySecretKey());

        $report = $client->usageReports->detailsForMonth(new Month(2021, 11));

        self::assertGreaterThan(0, count($report));
        self::assertGreaterThan(0, count($report[0]->usageByChart));
        $expected = new UsageByEvent(new Event(580293, "largeStadiumEvent", false), 143);
        self::assertEquals([$expected], $report[0]->usageByChart[0]->usageByEvent);
    }

    public function testUsageReportForEventInMonth()
    {
        $this->skipTestIfDemoCompanySecretKeyNotSet();
        $client = $this->createSeatsioClient($this->demoCompanySecretKey());

        $report = $client->usageReports->detailsForEventInMonth(580293, new Month(2021, 11));

        self::assertGreaterThan(0, count($report));
        $expected = new UsageForObjectV1('102-9-14', 0, null, 1, 1);
        self::assertEquals($expected, $report[0]);
    }

    private function skipTestIfDemoCompanySecretKeyNotSet(): void
    {
        if (!$this->isDemoCompanySecretKeySet()) {
            $this->markTestSkipped("DEMO_COMPANY_SECRET_KEY environment variable not set");
        }
    }
}
