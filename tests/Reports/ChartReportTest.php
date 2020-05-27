<?php

namespace Seatsio\Reports;

use Seatsio\SeatsioClientTest;

class ChartReportsTest extends SeatsioClientTest
{

    public function testReportItemProperties()
    {
        $chartKey = $this->createTestChart();

        $report = $this->seatsioClient->chartReports->byLabel($chartKey);

        $reportItem = $report["A-1"][0];
        self::assertEquals("A-1", $reportItem->label);
        self::assertEquals(someLabels("1", "seat", "A", "row"), $reportItem->labels);
        self::assertEquals("Cat1", $reportItem->categoryLabel);
        self::assertEquals(9, $reportItem->categoryKey);
        self::assertEquals("seat", $reportItem->objectType);
        self::assertNull($reportItem->section);
        self::assertNull($reportItem->entrance);
        self::assertNull($reportItem->leftNeighbour);
        self::assertEquals("A-2", $reportItem->rightNeighbour);
    }

    public function testReportItemPropertiesForGA()
    {
        $chartKey = $this->createTestChart();

        $report = $this->seatsioClient->chartReports->byLabel($chartKey);

        $reportItem = $report["GA1"][0];
        self::assertEquals(100, $reportItem->capacity);
        self::assertEquals("generalAdmission", $reportItem->objectType);
    }

    public function testByLabel()
    {
        $chartKey = $this->createTestChart();

        $report = $this->seatsioClient->chartReports->byLabel($chartKey);
        self::assertCount(1, $report["A-1"]);
        self::assertCount(1, $report["A-2"]);
    }


    public function testByCategoryKey()
    {
        $chartKey = $this->createTestChart();

        $report = $this->seatsioClient->chartReports->byCategoryKey($chartKey);
        self::assertCount(17, $report["9"]);
        self::assertCount(17, $report["10"]);
    }

    public function testByCategoryLabel()
    {
        $chartKey = $this->createTestChart();

        $report = $this->seatsioClient->chartReports->byCategoryLabel($chartKey);
        self::assertCount(17, $report["Cat1"]);
        self::assertCount(17, $report["Cat2"]);
    }

}
