<?php

namespace Reports\Charts;

use PHPUnit\Framework\Attributes\DataProvider;
use Seatsio\Common\Floor;
use Seatsio\Common\IDs;
use Seatsio\Reports\Charts\ChartReports;
use Seatsio\SeatsioClient;
use Seatsio\SeatsioClientTest;

class ChartReportsTest extends SeatsioClientTest
{

    private static function noChartUpdate(): \Closure
    {
        return function(SeatsioClient $client, string $chartKey) {
            // no-op
        };
    }

    private static function createDraftReport(): \Closure
    {
        return function(SeatsioClient $client, string $chartKey) {
            $client->events->create($chartKey);
            $client->charts->update($chartKey, "foo");
        };
    }

    public static function byLabelDataProvider(): array
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
            array(ChartReportsTest::noChartUpdate(), $getReport),
            array(ChartReportsTest::createDraftReport(), $getDraftReport)
        );
    }

    public static function byLabelAndChart(): array {
        return ChartReportsTest::byLabelAndTableBookingModelDataProvider("chart");
    }

    public static function byLabelAndTrue(): array {
        return ChartReportsTest::byLabelAndTableBookingModelDataProvider("true");
    }

    public static function byLabelAndFalse(): array {
        return ChartReportsTest::byLabelAndTableBookingModelDataProvider("false");
    }

    private static function byLabelAndTableBookingModelDataProvider(string $tableBookingMode): array
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
            array(ChartReportsTest::noChartUpdate(), $getReport),
            array(ChartReportsTest::createDraftReport(), $getDraftReport)
        );
    }

    #[DataProvider("byLabelDataProvider")]
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
        self::assertFalse($reportItem->isAccessible);
        self::assertFalse($reportItem->isCompanionSeat);
        self::assertFalse($reportItem->hasRestrictedView);
        self::assertFalse($reportItem->hasLiftUpArmrests);
        self::assertFalse($reportItem->isHearingImpaired);
        self::assertFalse($reportItem->isSemiAmbulatorySeat);
        self::assertFalse($reportItem->hasSignLanguageInterpretation);
        self::assertFalse($reportItem->isPlusSize);
        self::assertNull($reportItem->floor);
    }

    #[DataProvider("byLabelDataProvider")]
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

    #[DataProvider("byLabelAndTrue")]
    public function testReportItemPropertiesForTable($updateChart, $getReport)
    {
        $chartKey = $this->createTestChartWithTables();
        $updateChart($this->seatsioClient, $chartKey);

        $report = $getReport($this->seatsioClient, $chartKey);

        $reportItem = $report["T1"][0];
        self::assertEquals(false, $reportItem->bookAsAWhole);
        self::assertEquals(6, $reportItem->numSeats);
    }

    #[DataProvider("byLabelDataProvider")]
    public function testByLabel($updateChart, $getReport)
    {
        $chartKey = $this->createTestChart();
        $updateChart($this->seatsioClient, $chartKey);

        $report = $getReport($this->seatsioClient, $chartKey);

        self::assertCount(1, $report["A-1"]);
        self::assertCount(1, $report["A-2"]);
    }

    #[DataProvider("byLabelDataProvider")]
    public function testByLabelWithFloor($updateChart, $getReport)
    {
        $chartKey = $this->createTestChartWithFloors();
        $updateChart($this->seatsioClient, $chartKey);

        $report = $getReport($this->seatsioClient, $chartKey);

        $floors = self::floors();
        self::assertEquals($floors[0], $report["S1-A-1"][0]->floor);
        self::assertEquals($floors[0], $report["S1-A-2"][0]->floor);
        self::assertEquals($floors[1], $report["S2-B-1"][0]->floor);
        self::assertEquals($floors[1], $report["S2-B-2"][0]->floor);
    }

    #[DataProvider("byObjectTypeDataProvider")]
    public function testByObjectType($updateChart, $getReport)
    {
        $chartKey = $this->createTestChart();
        $updateChart($this->seatsioClient, $chartKey);

        $report = $getReport($this->seatsioClient, $chartKey);

        self::assertCount(32, $report["seat"]);
        self::assertCount(2, $report["generalAdmission"]);
    }

    #[DataProvider("byObjectTypeDataProvider")]
    public function testByObjectTypeWithFloors($updateChart, $getReport)
    {
        $chartKey = $this->createTestChartWithFloors();
        $updateChart($this->seatsioClient, $chartKey);

        $report = $getReport($this->seatsioClient, $chartKey);

        $floors = self::floors();
        self::assertEquals($floors[0], $report["seat"][0]->floor);
        self::assertEquals($floors[0], $report["seat"][1]->floor);
        self::assertEquals($floors[1], $report["seat"][2]->floor);
        self::assertEquals($floors[1], $report["seat"][3]->floor);
    }

    public static function byObjectTypeDataProvider(): array
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
            array(ChartReportsTest::noChartUpdate(), $getReport),
            array(ChartReportsTest::createDraftReport(), $getDraftReport)
        );
    }

    #[DataProvider("byLabelDataProvider")]
    public function testByLabel_bookWholeTablesNull($updateChart, $getReport)
    {
        $chartKey = $this->createTestChartWithTables();
        $updateChart($this->seatsioClient, $chartKey);

        $report = $getReport($this->seatsioClient, $chartKey);

        self::assertCount(14, array_keys($report));
    }

    #[DataProvider("byLabelAndChart")]
    public function testByLabel_bookWholeTablesChart($updateChart, $getReport)
    {
        $chartKey = $this->createTestChartWithTables();
        $updateChart($this->seatsioClient, $chartKey);

        $report = $getReport($this->seatsioClient, $chartKey);

        self::assertCount(7, array_keys($report));
    }

    #[DataProvider("byLabelAndTrue")]
    public function testByLabel_bookWholeTablesTrue($updateChart, $getReport)
    {
        $chartKey = $this->createTestChartWithTables();
        $updateChart($this->seatsioClient, $chartKey);

        $report = $getReport($this->seatsioClient, $chartKey);

        self::assertCount(2, array_keys($report));
    }

    #[DataProvider("byLabelAndFalse")]
    public function testByLabel_bookWholeTablesFalse($updateChart, $getReport)
    {
        $chartKey = $this->createTestChartWithTables();
        $updateChart($this->seatsioClient, $chartKey);

        $report = $getReport($this->seatsioClient, $chartKey);

        self::assertCount(12, array_keys($report));
    }

    #[DataProvider("byCategoryKeyDataProvider")]
    public function testByCategoryKey($updateChart, $getReport)
    {
        $chartKey = $this->createTestChart();
        $updateChart($this->seatsioClient, $chartKey);

        $report = $this->seatsioClient->chartReports->byCategoryKey($chartKey);

        self::assertCount(17, $report["9"]);
        self::assertCount(17, $report["10"]);
    }

    #[DataProvider("byCategoryKeyDataProvider")]
    public function testByCategoryKeyWithFloor($updateChart, $getReport)
    {
        $chartKey = $this->createTestChartWithFloors();
        $updateChart($this->seatsioClient, $chartKey);

        $report = $this->seatsioClient->chartReports->byCategoryKey($chartKey);

        $floors = self::floors();
        self::assertEquals($floors[0], $report["1"][0]->floor);
        self::assertEquals($floors[0], $report["1"][1]->floor);
        self::assertEquals($floors[1], $report["2"][0]->floor);
        self::assertEquals($floors[1], $report["2"][1]->floor);
    }

    public static function byCategoryKeyDataProvider(): array
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
            array(ChartReportsTest::noChartUpdate(), $getReport),
            array(ChartReportsTest::createDraftReport(), $getDraftReport)
        );
    }

    #[DataProvider("byCategoryLabelDataProvider")]
    public function testByCategoryLabel($updateChart, $getReport)
    {
        $chartKey = $this->createTestChart();
        $updateChart($this->seatsioClient, $chartKey);

        $report = $this->seatsioClient->chartReports->byCategoryLabel($chartKey);

        self::assertCount(17, $report["Cat1"]);
        self::assertCount(17, $report["Cat2"]);
    }

    #[DataProvider("byCategoryLabelDataProvider")]
    public function testByCategoryLabelWithFloors($updateChart, $getReport)
    {
        $chartKey = $this->createTestChartWithFloors();
        $updateChart($this->seatsioClient, $chartKey);

        $report = $this->seatsioClient->chartReports->byCategoryLabel($chartKey);

        $floors = self::floors();
        self::assertEquals($floors[0], $report["CatA"][0]->floor);
        self::assertEquals($floors[0], $report["CatA"][1]->floor);
        self::assertEquals($floors[1], $report["CatB"][0]->floor);
        self::assertEquals($floors[1], $report["CatB"][1]->floor);
    }

    public static function byCategoryLabelDataProvider(): array
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
            array(ChartReportsTest::noChartUpdate(), $getReport),
            array(ChartReportsTest::createDraftReport(), $getDraftReport)
        );
    }

    #[DataProvider("bySectionDataProvider")]
    public function testBySection($updateChart, $getReport)
    {
        $chartKey = $this->createTestChartWithSections();
        $updateChart($this->seatsioClient, $chartKey);

        $report = $getReport($this->seatsioClient, $chartKey);

        self::assertCount(36, $report["Section A"]);
        self::assertCount(35, $report["Section B"]);
    }

    #[DataProvider("bySectionDataProvider")]
    public function testBySectionWithFloors($updateChart, $getReport)
    {
        $chartKey = $this->createTestChartWithFloors();
        $updateChart($this->seatsioClient, $chartKey);

        $report = $getReport($this->seatsioClient, $chartKey);

        $floors = self::floors();
        self::assertEquals($floors[0], $report["S1"][0]->floor);
        self::assertEquals($floors[0], $report["S1"][1]->floor);
        self::assertEquals($floors[1], $report["S2"][0]->floor);
        self::assertEquals($floors[1], $report["S2"][1]->floor);
    }

    public static function bySectionDataProvider(): array
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
            array(ChartReportsTest::noChartUpdate(), $getReport),
            array(ChartReportsTest::createDraftReport(), $getDraftReport)
        );
    }

    #[DataProvider("byZoneDataProvider")]
    public function testByZone($updateChart, $getReport)
    {
        $chartKey = $this->createTestChartWithZones();
        $updateChart($this->seatsioClient, $chartKey);

        $report = $getReport($this->seatsioClient, $chartKey);

        self::assertCount(6032, $report["midtrack"]);
        self::assertEquals("midtrack", $report["midtrack"][0]->zone);
        self::assertCount(2865, $report["finishline"]);
    }

    public static function byZoneDataProvider(): array
    {
        $getReport = function(SeatsioClient $client, string $chartKey) {
            $byZone = $client->chartReports->byZone($chartKey);
            return $byZone;
        };
        $getDraftReport = function(SeatsioClient $client, string $chartKey) {
            $byZone = $client->chartReports->byZone($chartKey, null, "draft");
            return $byZone;
        };
        return array(
            array(ChartReportsTest::noChartUpdate(), $getReport),
            array(ChartReportsTest::createDraftReport(), $getDraftReport)
        );
    }

    private static function floors(): array
    {
        $floor1 = new Floor();
        $floor1->name = "1";
        $floor1->displayName = "Floor 1";

        $floor2 = new Floor();
        $floor2->name = "2";
        $floor2->displayName = "Floor 2";

        return [$floor1, $floor2];
    }
}
