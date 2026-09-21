<?php

namespace Reports\Usage;

use PHPUnit\Framework\TestCase;
use Seatsio\Region;
use Seatsio\Reports\Usage\DetailsForEventInMonth\UsageForObjectV1;
use Seatsio\Reports\Usage\DetailsForMonth\Event;
use Seatsio\Reports\Usage\DetailsForMonth\UsageByEvent;
use Seatsio\Reports\Usage\SummaryForMonths\Month;
use Seatsio\SeatsioClient;

class UsageReportTest extends TestCase
{
    public function testUsageReportForAllMonths()
    {
        $client = $this->usageReportingClient();

        $report = $client->usageReports->summaryForAllMonths();

        self::assertNotNull($report->usageCutoffDate);
        self::assertGreaterThan(0, count($report->usage));
        self::assertEquals(new Month(2014, 2), $report->usage[0]->month);
    }

    public function testUsageReportForMonth()
    {
        $client = $this->usageReportingClient();

        $report = $client->usageReports->detailsForMonth(new Month(2021, 11));

        self::assertGreaterThan(0, count($report));
        self::assertGreaterThan(0, count($report[0]->usageByChart));
        $expected = new UsageByEvent(new Event(580293, "largeStadiumEvent", false), 143);
        self::assertEquals([$expected], $report[0]->usageByChart[0]->usageByEvent);
    }

    public function testUsageReportForEventInMonth()
    {
        $client = $this->usageReportingClient();

        $report = $client->usageReports->detailsForEventInMonth(580293, new Month(2021, 11));

        self::assertGreaterThan(0, count($report));
        $expected = new UsageForObjectV1('102-9-14', 0, null, 1, 1);
        self::assertEquals($expected, $report[0]);
    }

    private function usageReportingClient(): SeatsioClient
    {
        if (!self::isConfigured()) {
            $this->markTestSkipped("USAGE_REPORTING_TESTS_API_URL and USAGE_REPORTING_TESTS_SECRET_KEY environment variables not set");
        }
        return new SeatsioClient(Region::withUrl(self::apiUrl()), self::secretKey());
    }

    private static function apiUrl()
    {
        return getenv('USAGE_REPORTING_TESTS_API_URL');
    }

    private static function secretKey()
    {
        return getenv('USAGE_REPORTING_TESTS_SECRET_KEY');
    }

    private static function isConfigured(): bool
    {
        return self::isSet(self::apiUrl()) && self::isSet(self::secretKey());
    }

    private static function isSet($value): bool
    {
        return $value !== false && trim($value) !== '';
    }
}
