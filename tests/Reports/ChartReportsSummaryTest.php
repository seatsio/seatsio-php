<?php

namespace Seatsio\Reports;

use Seatsio\Events\Channel;
use Seatsio\Events\ObjectProperties;
use Seatsio\SeatsioClientTest;

class ChartReportsSummaryTest extends SeatsioClientTest
{
    public function testSummaryByObjectType()
    {
        $chartKey = $this->createTestChart();

        $report = $this->seatsioClient->chartReports->summaryByObjectType($chartKey);

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

    public function testSummaryByCategoryKey()
    {
        $chartKey = $this->createTestChart();

        $report = $this->seatsioClient->chartReports->summaryByCategoryKey($chartKey);

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
            'NO_CATEGORY' => [
                'count' => 0,
                'bySection' => [],
                'byObjectType' => []
            ]
        ];
        self::assertEquals($expectedReport, $report);
    }

    public function testSummaryByCategoryLabel()
    {
        $chartKey = $this->createTestChart();

        $report = $this->seatsioClient->chartReports->summaryByCategoryLabel($chartKey);

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
            'NO_CATEGORY' => [
                'count' => 0,
                'bySection' => [],
                'byObjectType' => []
            ]
        ];
        self::assertEquals($expectedReport, $report);
    }

    public function testSummaryBySection()
    {
        $chartKey = $this->createTestChart();

        $report = $this->seatsioClient->chartReports->summaryBySection($chartKey);

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
}
