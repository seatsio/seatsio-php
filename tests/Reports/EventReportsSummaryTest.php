<?php

namespace Seatsio\Reports;

use Seatsio\Events\Channel;
use Seatsio\Events\ObjectProperties;
use Seatsio\SeatsioClientTest;

class EventReportsSummaryTest extends SeatsioClientTest
{

    public function testSummaryByStatus()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey);
        $this->seatsioClient->events->book($event->key, (new ObjectProperties("A-1")));

        $report = $this->seatsioClient->eventReports->summaryByStatus($event->key);

        $expectedReport = [
            'free' => [
                'count' => 231,
                'byCategoryKey' => [9 => 115, 10 => 116],
                'byCategoryLabel' => ['Cat2' => 116, 'Cat1' => 115],
                'bySection' => ['NO_SECTION' => 231],
                'bySelectability' => ['selectable' => 231],
                'byChannel' => ['NO_CHANNEL' => 231],
                'byObjectType' => ['seat' => 31, 'generalAdmission' => 200]
            ],
            'booked' => [
                'count' => 1,
                'byCategoryKey' => [9 => 1],
                'byCategoryLabel' => ['Cat1' => 1],
                'bySection' => ['NO_SECTION' => 1],
                'bySelectability' => ['not_selectable' => 1],
                'byChannel' => ['NO_CHANNEL' => 1],
                'byObjectType' => ['seat' => 1]
            ]
        ];
        self::assertEquals($expectedReport, $report);
    }

    public function testSummaryByObjectType()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey);

        $report = $this->seatsioClient->eventReports->summaryByObjectType($event->key);

        $expectedReport = [
            'seat' => [
                'count' => 32,
                'byCategoryKey' => [9 => 16, 10 => 16],
                'byCategoryLabel' => ['Cat2' => 16, 'Cat1' => 16],
                'bySection' => ['NO_SECTION' => 32],
                'bySelectability' => ['selectable' => 32],
                'byChannel' => ['NO_CHANNEL' => 32],
                'byStatus' => ['free' => 32]
            ],
            'generalAdmission' => [
                'count' => 200,
                'byCategoryKey' => [9 => 100, 10 => 100],
                'byCategoryLabel' => ['Cat1' => 100, 'Cat2' => 100],
                'bySection' => ['NO_SECTION' => 200],
                'bySelectability' => ['selectable' => 200],
                'byChannel' => ['NO_CHANNEL' => 200],
                'byStatus' => ['free' => 200]
            ],
            'table' => [
                'count' => 0,
                'byCategoryKey' => [],
                'byCategoryLabel' => [],
                'bySection' => [],
                'bySelectability' => [],
                'byChannel' => [],
                'byStatus' => []
            ],
            'booth' => [
                'count' => 0,
                'byCategoryKey' => [],
                'byCategoryLabel' => [],
                'bySection' => [],
                'bySelectability' => [],
                'byChannel' => [],
                'byStatus' => []
            ]
        ];
        self::assertEquals($expectedReport, $report);
    }

    public function testSummaryByCategoryKey()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey);
        $this->seatsioClient->events->book($event->key, (new ObjectProperties("A-1")));

        $report = $this->seatsioClient->eventReports->summaryByCategoryKey($event->key);

        $expectedReport = [
            '9' => [
                'count' => 116,
                'bySection' => ['NO_SECTION' => 116],
                'byStatus' => ['free' => 115, 'booked' => 1],
                'bySelectability' => ['selectable' => 115, 'not_selectable' => 1],
                'byChannel' => ['NO_CHANNEL' => 116],
                'byObjectType' => ['seat' => 16, 'generalAdmission' => 100]
            ],
            '10' => [
                'count' => 116,
                'bySection' => ['NO_SECTION' => 116],
                'byStatus' => ['free' => 116],
                'bySelectability' => ['selectable' => 116],
                'byChannel' => ['NO_CHANNEL' => 116],
                'byObjectType' => ['seat' => 16, 'generalAdmission' => 100]
            ],
            'NO_CATEGORY' => [
                'count' => 0,
                'bySection' => [],
                'byStatus' => [],
                'bySelectability' => [],
                'byChannel' => [],
                'byObjectType' => []
            ]
        ];
        self::assertEquals($expectedReport, $report);
    }

    public function testSummaryByCategoryLabel()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey);
        $this->seatsioClient->events->book($event->key, (new ObjectProperties("A-1")));

        $report = $this->seatsioClient->eventReports->summaryByCategoryLabel($event->key);

        $expectedReport = [
            'Cat1' => [
                'count' => 116,
                'bySection' => ['NO_SECTION' => 116],
                'byStatus' => ['free' => 115, 'booked' => 1],
                'bySelectability' => ['selectable' => 115, 'not_selectable' => 1],
                'byChannel' => ['NO_CHANNEL' => 116],
                'byObjectType' => ['seat' => 16, 'generalAdmission' => 100]
            ],
            'Cat2' => [
                'count' => 116,
                'bySection' => ['NO_SECTION' => 116],
                'byStatus' => ['free' => 116],
                'bySelectability' => ['selectable' => 116],
                'byChannel' => ['NO_CHANNEL' => 116],
                'byObjectType' => ['seat' => 16, 'generalAdmission' => 100]
            ],
            'NO_CATEGORY' => [
                'count' => 0,
                'bySection' => [],
                'byStatus' => [],
                'bySelectability' => [],
                'byChannel' => [],
                'byObjectType' => []
            ]
        ];
        self::assertEquals($expectedReport, $report);
    }

    public function testSummaryBySection()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey);
        $this->seatsioClient->events->book($event->key, (new ObjectProperties("A-1")));

        $report = $this->seatsioClient->eventReports->summaryBySection($event->key);

        $expectedReport = [
            'NO_SECTION' => [
                'count' => 232,
                'byCategoryKey' => [9 => 116, 10 => 116],
                'byCategoryLabel' => ['Cat2' => 116, 'Cat1' => 116],
                'byStatus' => ['free' => 231, 'booked' => 1],
                'bySelectability' => ['selectable' => 231, 'not_selectable' => 1],
                'byChannel' => ['NO_CHANNEL' => 232],
                'byObjectType' => ['seat' => 32, 'generalAdmission' => 200]
            ]
        ];
        self::assertEquals($expectedReport, $report);
    }

    public function testSummaryBySelectability()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey);
        $this->seatsioClient->events->book($event->key, (new ObjectProperties("A-1")));

        $report = $this->seatsioClient->eventReports->summaryBySelectability($event->key);

        $expectedReport = [
            'selectable' => [
                'count' => 231,
                'byCategoryKey' => [9 => 115, 10 => 116],
                'byCategoryLabel' => ['Cat2' => 116, 'Cat1' => 115],
                'bySection' => ['NO_SECTION' => 231],
                'byStatus' => ['free' => 231],
                'byChannel' => ['NO_CHANNEL' => 231],
                'byObjectType' => ['seat' => 31, 'generalAdmission' => 200]
            ],
            'not_selectable' => [
                'count' => 1,
                'byCategoryKey' => [9 => 1],
                'byCategoryLabel' => ['Cat1' => 1],
                'bySection' => ['NO_SECTION' => 1],
                'byStatus' => ['booked' => 1],
                'byChannel' => ['NO_CHANNEL' => 1],
                'byObjectType' => ['seat' => 1]
            ]
        ];
        self::assertEquals($expectedReport, $report);
    }

    public function testSummaryByChannel()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey);
        $this->seatsioClient->events->updateChannels($event->key, [
            "channel1" => new Channel("channel 1", "#FF0000", 1)
        ]);
        $this->seatsioClient->events->assignObjectsToChannels($event->key, ["channel1" => ["A-1", "A-2"]]);

        $report = $this->seatsioClient->eventReports->summaryByChannel($event->key);

        $expectedReport = [
            'NO_CHANNEL' => [
                'count' => 230,
                'byCategoryKey' => [9 => 114, 10 => 116],
                'byCategoryLabel' => ['Cat2' => 116, 'Cat1' => 114],
                'bySection' => ['NO_SECTION' => 230],
                'byStatus' => ['free' => 230],
                'bySelectability' => ['selectable' => 230],
                'byObjectType' => ['seat' => 30, 'generalAdmission' => 200]
            ],
            'channel1' => [
                'count' => 2,
                'byCategoryKey' => [9 => 2],
                'byCategoryLabel' => ['Cat1' => 2],
                'bySection' => ['NO_SECTION' => 2],
                'byStatus' => ['free' => 2],
                'bySelectability' => ['selectable' => 2],
                'byObjectType' => ['seat' => 2]
            ]
        ];
        self::assertEquals($expectedReport, $report);
    }

}
