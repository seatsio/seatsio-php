<?php

namespace Reports\Charts;

use Seatsio\Common\IDs;
use Seatsio\SeatsioClient;
use Seatsio\SeatsioClientTest;

class ChartReportsTest extends SeatsioClientTest
{

    private function noChartUpdate(): \Closure
    {
        return function(SeatsioClient $client, string $chartKey) {
            // no-op
        };
    }

    private function createDraftReport(): \Closure
    {
        return function(SeatsioClient $client, string $chartKey) {
            $client->events->create($chartKey);
            $client->charts->update($chartKey, "foo");
        };
    }

    public function byLabelDataProvider(): array
    {
        $getReport = function(SeatsioClient $client, string $chartKey)
        {
            return $client->chartReports->byLabel($chartKey);
        };
        $getDraftReport = function(SeatsioClient $client, string $chartKey)
        {
            return $client->chartReports->byLabel($chartKey, null, "draft");
        };
        return array(
            array($this->noChartUpdate(), $getReport),
            array($this->createDraftReport(), $getDraftReport)
        );
    }

    public function byLabelAndChart(): array {
        return $this->byLabelAndTableBookingModelDataProvider("chart");
    }

    public function byLabelAndTrue(): array {
        return $this->byLabelAndTableBookingModelDataProvider("true");
    }

    public function byLabelAndFalse(): array {
        return $this->byLabelAndTableBookingModelDataProvider("false");
    }

    private function byLabelAndTableBookingModelDataProvider(string $tableBookingMode): array
    {
        $getReport = function(SeatsioClient $client, string $chartKey) use ($tableBookingMode)
        {
            return $client->chartReports->byLabel($chartKey, $tableBookingMode);
        };
        $getDraftReport = function(SeatsioClient $client, string $chartKey) use ($tableBookingMode)
        {
            return $client->chartReports->byLabel($chartKey, $tableBookingMode, "draft");
        };
        return array(
            array($this->noChartUpdate(), $getReport),
            array($this->createDraftReport(), $getDraftReport)
        );
    }

    /**
     * @dataProvider byLabelDataProvider
     */
    public function testReportItemProperties($updateChart, $getReport)
    {
        $chartKey = $this->createTestChart();
        $updateChart($this->seatsioClient, $chartKey);

        $report = $getReport($this->seatsioClient, $chartKey);

        $reportItem = $report["A-1"][0];
        self::assertEquals("A-1", $reportItem->label);
        self::assertEquals(someLabels("1", "seat", "A", "row"), $reportItem->labels);
        self::assertEquals(new IDs("1", "A", null), $reportItem->ids);
        self::assertEquals("Cat1", $reportItem->categoryLabel);
        self::assertEquals(9, $reportItem->categoryKey);
        self::assertEquals("seat", $reportItem->objectType);
        self::assertNull($reportItem->section);
        self::assertNull($reportItem->entrance);
        self::assertNull($reportItem->leftNeighbour);
        self::assertEquals("A-2", $reportItem->rightNeighbour);
        self::assertNotNull($reportItem->distanceToFocalPoint);
        self::assertNotNull($reportItem->isAccessible);
        self::assertNotNull($reportItem->isCompanionSeat);
        self::assertNotNull($reportItem->hasRestrictedView);
    }

    /**
     * @dataProvider byLabelDataProvider
     */
    public function testReportItemPropertiesForGA($updateChart, $getReport)
    {
        $chartKey = $this->createTestChart();
        $updateChart($this->seatsioClient, $chartKey);

        $report = $getReport($this->seatsioClient, $chartKey);

        $reportItem = $report["GA1"][0];
        self::assertEquals(100, $reportItem->capacity);
        self::assertEquals("generalAdmission", $reportItem->objectType);
        self::assertEquals(false, $reportItem->bookAsAWhole);
    }

    /**
     * @dataProvider byLabelAndTrue
     */
    public function testReportItemPropertiesForTable($updateChart, $getReport)
    {
        $chartKey = $this->createTestChartWithTables();
        $updateChart($this->seatsioClient, $chartKey);

        $report = $getReport($this->seatsioClient, $chartKey);

        $reportItem = $report["T1"][0];
        self::assertEquals(false, $reportItem->bookAsAWhole);
        self::assertEquals(6, $reportItem->numSeats);
    }

    /**
     * @dataProvider byLabelDataProvider
     */
    public function testByLabel($updateChart, $getReport)
    {
        $chartKey = $this->createTestChart();
        $updateChart($this->seatsioClient, $chartKey);

        $report = $getReport($this->seatsioClient, $chartKey);

        self::assertCount(1, $report["A-1"]);
        self::assertCount(1, $report["A-2"]);
    }

    /**
     * @dataProvider byObjectTypeDataProvider
     */
    public function testByObjectType($updateChart, $getReport)
    {
        $chartKey = $this->createTestChart();
        $updateChart($this->seatsioClient, $chartKey);

        $report = $getReport($this->seatsioClient, $chartKey);

        self::assertCount(32, $report["seat"]);
        self::assertCount(2, $report["generalAdmission"]);
    }

    public function byObjectTypeDataProvider(): array
    {
        $getReport = function(SeatsioClient $client, string $chartKey)
        {
            return $client->chartReports->byObjectType($chartKey);
        };
        $getDraftReport = function(SeatsioClient $client, string $chartKey)
        {
            return $client->chartReports->byObjectType($chartKey, null, "draft");
        };
        return array(
            array($this->noChartUpdate(), $getReport),
            array($this->createDraftReport(), $getDraftReport)
        );
    }

    /**
     * @dataProvider byLabelDataProvider
     */
    public function testByLabel_bookWholeTablesNull($updateChart, $getReport)
    {
        $chartKey = $this->createTestChartWithTables();
        $updateChart($this->seatsioClient, $chartKey);

        $report = $getReport($this->seatsioClient, $chartKey);

        self::assertCount(14, array_keys($report));
    }

    /**
     * @dataProvider byLabelAndChart
     */
    public function testByLabel_bookWholeTablesChart($updateChart, $getReport)
    {
        $chartKey = $this->createTestChartWithTables();
        $updateChart($this->seatsioClient, $chartKey);

        $report = $getReport($this->seatsioClient, $chartKey);

        self::assertCount(7, array_keys($report));
    }

    /**
     * @dataProvider byLabelAndTrue
     */
    public function testByLabel_bookWholeTablesTrue($updateChart, $getReport)
    {
        $chartKey = $this->createTestChartWithTables();
        $updateChart($this->seatsioClient, $chartKey);

        $report = $getReport($this->seatsioClient, $chartKey);

        self::assertCount(2, array_keys($report));
    }

    /**
     * @dataProvider byLabelAndFalse
     */
    public function testByLabel_bookWholeTablesFalse($updateChart, $getReport)
    {
        $chartKey = $this->createTestChartWithTables();
        $updateChart($this->seatsioClient, $chartKey);

        $report = $getReport($this->seatsioClient, $chartKey);

        self::assertCount(12, array_keys($report));
    }

    /**
     * @dataProvider byCategoryKeyDataProvider
     */
    public function testByCategoryKey($updateChart, $getReport)
    {
        $chartKey = $this->createTestChart();
        $updateChart($this->seatsioClient, $chartKey);

        $report = $this->seatsioClient->chartReports->byCategoryKey($chartKey);

        self::assertCount(17, $report["9"]);
        self::assertCount(17, $report["10"]);
    }

    public function byCategoryKeyDataProvider(): array
    {
        $getReport = function(SeatsioClient $client, string $chartKey) {
            $bySection = $client->chartReports->byCategoryKey($chartKey);
            return $bySection;
        };
        $getDraftReport = function(SeatsioClient $client, string $chartKey) {
            $bySection = $client->chartReports->byCategoryKey($chartKey, null, "draft");
            return $bySection;
        };
        return array(
            array($this->noChartUpdate(), $getReport),
            array($this->createDraftReport(), $getDraftReport)
        );
    }

    /**
     * @dataProvider byCategoryLabelDataProvider
     */
    public function testByCategoryLabel($updateChart, $getReport)
    {
        $chartKey = $this->createTestChart();
        $updateChart($this->seatsioClient, $chartKey);

        $report = $this->seatsioClient->chartReports->byCategoryLabel($chartKey);

        self::assertCount(17, $report["Cat1"]);
        self::assertCount(17, $report["Cat2"]);
    }

    public function byCategoryLabelDataProvider(): array
    {
        $getReport = function(SeatsioClient $client, string $chartKey) {
            $bySection = $client->chartReports->byCategoryLabel($chartKey);
            return $bySection;
        };
        $getDraftReport = function(SeatsioClient $client, string $chartKey) {
            $bySection = $client->chartReports->byCategoryLabel($chartKey, null, "draft");
            return $bySection;
        };
        return array(
            array($this->noChartUpdate(), $getReport),
            array($this->createDraftReport(), $getDraftReport)
        );
    }

    /**
     * @dataProvider bySectionDataProvider
     */
    public function testBySection($updateChart, $getReport)
    {
        $chartKey = $this->createTestChartWithSections();
        $updateChart($this->seatsioClient, $chartKey);

        $report = $getReport($this->seatsioClient, $chartKey);

        self::assertCount(36, $report["Section A"]);
        self::assertCount(35, $report["Section B"]);
    }

    public function bySectionDataProvider(): array
    {
        $getReport = function(SeatsioClient $client, string $chartKey) {
            $bySection = $client->chartReports->bySection($chartKey);
            return $bySection;
        };
        $getDraftReport = function(SeatsioClient $client, string $chartKey) {
            $bySection = $client->chartReports->bySection($chartKey, null, "draft");
            return $bySection;
        };
        return array(
            array($this->noChartUpdate(), $getReport),
            array($this->createDraftReport(), $getDraftReport)
        );
    }
}
