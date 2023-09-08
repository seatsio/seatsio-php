<?php

namespace Seatsio\Reports;

use Seatsio\SeatsioClient;
use Seatsio\SeatsioClientTest;

class ChartReportsSummaryTest extends SeatsioClientTest
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

    /**
     * @dataProvider summaryByObjectTypeDataProvider
     */
    public function testSummaryByObjectType($updateChart, $getReport)
    {
        $chartKey = $this->createTestChart();
        $updateChart($this->seatsioClient, $chartKey);

        $report = $getReport($this->seatsioClient, $chartKey);

        $expectedReport = [
            'seat' => [
                'count' => 32,
                'byCategoryKey' => [9 => 16, 10 => 16],
                'byCategoryLabel' => ['Cat2' => 16, 'Cat1' => 16],
                'bySection' => ['NO_SECTION' => 32]
            ],
            'generalAdmission' => [
                'count' => 200,
                'byCategoryKey' => [9 => 100, 10 => 100],
                'byCategoryLabel' => ['Cat1' => 100, 'Cat2' => 100],
                'bySection' => ['NO_SECTION' => 200]
            ],
            'table' => [
                'count' => 0,
                'byCategoryKey' => [],
                'byCategoryLabel' => [],
                'bySection' => []
            ],
            'booth' => [
                'count' => 0,
                'byCategoryKey' => [],
                'byCategoryLabel' => [],
                'bySection' => []
            ]
        ];
        self::assertEquals($expectedReport, $report);
    }

    public function summaryByObjectTypeDataProvider(): array
    {
        $getReport = function(SeatsioClient $client, string $chartKey)
        {
            return $client->chartReports->summaryByObjectType($chartKey);
        };
        $getDraftReport = function(SeatsioClient $client, string $chartKey)
        {
            return $client->chartReports->summaryByObjectType($chartKey, null, "draft");
        };
        return array(
            array($this->noChartUpdate(), $getReport),
            array($this->createDraftReport(), $getDraftReport)
        );
    }

    /**
     * @dataProvider  summaryByObjectTypeDataProvider_bookWholeTablesTrue
     */
    public function testSummaryByObjectType_bookWholeTablesTrue($updateChart, $getReport)
    {
        $chartKey = $this->createTestChartWithTables();
        $updateChart($this->seatsioClient, $chartKey);

        $report = $getReport($this->seatsioClient, $chartKey);

        $expectedReport = [
            'seat' => [
                'count' => 0,
                'byCategoryKey' => [],
                'byCategoryLabel' => [],
                'bySection' => []
            ],
            'generalAdmission' => [
                'count' => 0,
                'byCategoryKey' => [],
                'byCategoryLabel' => [],
                'bySection' => []
            ],
            'table' => [
                'count' => 2,
                'byCategoryKey' => [9 => 2],
                'byCategoryLabel' => ['Cat1' => 2],
                'bySection' => ['NO_SECTION' => 2]
            ],
            'booth' => [
                'count' => 0,
                'byCategoryKey' => [],
                'byCategoryLabel' => [],
                'bySection' => []
            ]
        ];
        self::assertEquals($expectedReport, $report);
    }

    public function summaryByObjectTypeDataProvider_bookWholeTablesTrue(): array
    {
        $getReport = function(SeatsioClient $client, string $chartKey)
        {
            return $client->chartReports->summaryByObjectType($chartKey, 'true');
        };
        $getDraftReport = function(SeatsioClient $client, string $chartKey)
        {
            return $client->chartReports->summaryByObjectType($chartKey, 'true', "draft");
        };
        return array(
            array($this->noChartUpdate(), $getReport),
            array($this->createDraftReport(), $getDraftReport)
        );
    }

    /**
     * @dataProvider summaryByCategoryKeyDataProvider
     */
    public function testSummaryByCategoryKey($updateChart, $getReport)
    {
        $chartKey = $this->createTestChart();
        $updateChart($this->seatsioClient, $chartKey);

        $report = $getReport($this->seatsioClient, $chartKey);

        $expectedReport = [
            '9' => [
                'count' => 116,
                'bySection' => ['NO_SECTION' => 116],
                'byObjectType' => ['seat' => 16, 'generalAdmission' => 100]
            ],
            '10' => [
                'count' => 116,
                'bySection' => ['NO_SECTION' => 116],
                'byObjectType' => ['seat' => 16, 'generalAdmission' => 100]
            ],
            'string11' => [
                'count' => 0,
                'bySection' => [],
                'byObjectType' => []
            ],
            'NO_CATEGORY' => [
                'count' => 0,
                'bySection' => [],
                'byObjectType' => []
            ]
        ];
        self::assertEquals($expectedReport, $report);
    }

    public function summaryByCategoryKeyDataProvider(): array
    {
        $getReport = function(SeatsioClient $client, string $chartKey)
        {
            return $client->chartReports->summaryByCategoryKey($chartKey);
        };
        $getDraftReport = function(SeatsioClient $client, string $chartKey)
        {
            return $client->chartReports->summaryByCategoryKey($chartKey, null, "draft");
        };
        return array(
            array($this->noChartUpdate(), $getReport),
            array($this->createDraftReport(), $getDraftReport)
        );
    }

    /**
     * @dataProvider summaryByCategoryLabelDataProvider
     */
    public function testSummaryByCategoryLabel($updateChart, $getReport)
    {
        $chartKey = $this->createTestChart();
        $updateChart($this->seatsioClient, $chartKey);

        $report = $getReport($this->seatsioClient, $chartKey);

        $expectedReport = [
            'Cat1' => [
                'count' => 116,
                'bySection' => ['NO_SECTION' => 116],
                'byObjectType' => ['seat' => 16, 'generalAdmission' => 100]
            ],
            'Cat2' => [
                'count' => 116,
                'bySection' => ['NO_SECTION' => 116],
                'byObjectType' => ['seat' => 16, 'generalAdmission' => 100]
            ],
            'Cat3' => [
                'count' => 0,
                'bySection' => [],
                'byObjectType' => []
            ],
            'NO_CATEGORY' => [
                'count' => 0,
                'bySection' => [],
                'byObjectType' => []
            ]
        ];
        self::assertEquals($expectedReport, $report);
    }

    public function summaryByCategoryLabelDataProvider(): array
    {
        $getReport = function(SeatsioClient $client, string $chartKey)
        {
            return $client->chartReports->summaryByCategoryLabel($chartKey);
        };
        $getDraftReport = function(SeatsioClient $client, string $chartKey)
        {
            return $client->chartReports->summaryByCategoryLabel($chartKey, null, "draft");
        };
        return array(
            array($this->noChartUpdate(), $getReport),
            array($this->createDraftReport(), $getDraftReport)
        );
    }

    /**
     * @dataProvider summaryBySectionDataProvider
     */
    public function testSummaryBySection($updateChart, $getReport)
    {
        $chartKey = $this->createTestChart();
        $updateChart($this->seatsioClient, $chartKey);

        $report = $getReport($this->seatsioClient, $chartKey);

        $expectedReport = [
            'NO_SECTION' => [
                'count' => 232,
                'byCategoryKey' => [9 => 116, 10 => 116],
                'byCategoryLabel' => ['Cat2' => 116, 'Cat1' => 116],
                'byObjectType' => ['seat' => 32, 'generalAdmission' => 200]
            ]
        ];
        self::assertEquals($expectedReport, $report);
    }

    public function summaryBySectionDataProvider(): array
    {
        $getReport = function(SeatsioClient $client, string $chartKey)
        {
            return $client->chartReports->summaryBySection($chartKey);
        };
        $getDraftReport = function(SeatsioClient $client, string $chartKey)
        {
            return $client->chartReports->summaryBySection($chartKey, null, "draft");
        };
        return array(
            array($this->noChartUpdate(), $getReport),
            array($this->createDraftReport(), $getDraftReport)
        );
    }
}
