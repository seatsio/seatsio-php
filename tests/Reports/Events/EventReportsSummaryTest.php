<?php

namespace Reports\Events;

use Seatsio\Events\Channel;
use Seatsio\Events\CreateEventParams;
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
                'byZone' => ['NO_ZONE' => 231],
                'bySelectability' => ['selectable' => 231],
                'byAvailability' => ['available' => 231],
                'byAvailabilityReason' => ['available' => 231],
                'byChannel' => ['NO_CHANNEL' => 231],
                'byObjectType' => ['seat' => 31, 'generalAdmission' => 200]
            ],
            'booked' => [
                'count' => 1,
                'byCategoryKey' => [9 => 1],
                'byCategoryLabel' => ['Cat1' => 1],
                'bySection' => ['NO_SECTION' => 1],
                'byZone' => ['NO_ZONE' => 1],
                'bySelectability' => ['not_selectable' => 1],
                'byAvailability' => ['not_available' => 1],
                'byAvailabilityReason' => ['booked' => 1],
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
                'byZone' => ['NO_ZONE' => 32],
                'byAvailability' => ['available' => 32],
                'byAvailabilityReason' => ['available' => 32],
                'bySelectability' => ['selectable' => 32],
                'byChannel' => ['NO_CHANNEL' => 32],
                'byStatus' => ['free' => 32]
            ],
            'generalAdmission' => [
                'count' => 200,
                'byCategoryKey' => [9 => 100, 10 => 100],
                'byCategoryLabel' => ['Cat1' => 100, 'Cat2' => 100],
                'bySection' => ['NO_SECTION' => 200],
                'byZone' => ['NO_ZONE' => 200],
                'byAvailability' => ['available' => 200],
                'byAvailabilityReason' => ['available' => 200],
                'bySelectability' => ['selectable' => 200],
                'byChannel' => ['NO_CHANNEL' => 200],
                'byStatus' => ['free' => 200]
            ],
            'table' => [
                'count' => 0,
                'byCategoryKey' => [],
                'byCategoryLabel' => [],
                'bySection' => [],
                'byZone' => [],
                'byAvailability' => [],
                'byAvailabilityReason' => [],
                'bySelectability' => [],
                'byChannel' => [],
                'byStatus' => []
            ],
            'booth' => [
                'count' => 0,
                'byCategoryKey' => [],
                'byCategoryLabel' => [],
                'bySection' => [],
                'byZone' => [],
                'byAvailability' => [],
                'byAvailabilityReason' => [],
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
                'byZone' => ['NO_ZONE' => 116],
                'byStatus' => ['free' => 115, 'booked' => 1],
                'byAvailability' => ['available' => 115, 'not_available' => 1],
                'byAvailabilityReason' => ['available' => 115, 'booked' => 1],
                'bySelectability' => ['selectable' => 115, 'not_selectable' => 1],
                'byChannel' => ['NO_CHANNEL' => 116],
                'byObjectType' => ['seat' => 16, 'generalAdmission' => 100]
            ],
            '10' => [
                'count' => 116,
                'bySection' => ['NO_SECTION' => 116],
                'byZone' => ['NO_ZONE' => 116],
                'byStatus' => ['free' => 116],
                'byAvailability' => ['available' => 116],
                'byAvailabilityReason' => ['available' => 116],
                'bySelectability' => ['selectable' => 116],
                'byChannel' => ['NO_CHANNEL' => 116],
                'byObjectType' => ['seat' => 16, 'generalAdmission' => 100]
            ],
            'string11' => [
                'count' => 0,
                'bySection' => [],
                'byZone' => [],
                'byStatus' => [],
                'byAvailability' => [],
                'byAvailabilityReason' => [],
                'bySelectability' => [],
                'byChannel' => [],
                'byObjectType' => []
            ],
            'NO_CATEGORY' => [
                'count' => 0,
                'bySection' => [],
                'byZone' => [],
                'byStatus' => [],
                'byAvailability' => [],
                'byAvailabilityReason' => [],
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
                'byZone' => ['NO_ZONE' => 116],
                'byStatus' => ['free' => 115, 'booked' => 1],
                'byAvailability' => ['available' => 115, 'not_available' => 1],
                'byAvailabilityReason' => ['available' => 115, 'booked' => 1],
                'bySelectability' => ['selectable' => 115, 'not_selectable' => 1],
                'byChannel' => ['NO_CHANNEL' => 116],
                'byObjectType' => ['seat' => 16, 'generalAdmission' => 100]
            ],
            'Cat2' => [
                'count' => 116,
                'bySection' => ['NO_SECTION' => 116],
                'byZone' => ['NO_ZONE' => 116],
                'byStatus' => ['free' => 116],
                'byAvailability' => ['available' => 116],
                'byAvailabilityReason' => ['available' => 116],
                'bySelectability' => ['selectable' => 116],
                'byChannel' => ['NO_CHANNEL' => 116],
                'byObjectType' => ['seat' => 16, 'generalAdmission' => 100]
            ],
            'Cat3' => [
                'count' => 0,
                'bySection' => [],
                'byZone' => [],
                'byStatus' => [],
                'byAvailability' => [],
                'byAvailabilityReason' => [],
                'bySelectability' => [],
                'byChannel' => [],
                'byObjectType' => []
            ],
            'NO_CATEGORY' => [
                'count' => 0,
                'bySection' => [],
                'byZone' => [],
                'byStatus' => [],
                'byAvailability' => [],
                'byAvailabilityReason' => [],
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
                'byAvailability' => ['available' => 231, 'not_available' => 1],
                'byAvailabilityReason' => ['available' => 231, 'booked' => 1],
                'bySelectability' => ['selectable' => 231, 'not_selectable' => 1],
                'byChannel' => ['NO_CHANNEL' => 232],
                'byObjectType' => ['seat' => 32, 'generalAdmission' => 200],
                'byZone' => ['NO_ZONE' => 232]
            ]
        ];
        self::assertEquals($expectedReport, $report);
    }

    public function testSummaryByZone()
    {
        $chartKey = $this->createTestChartWithZones();
        $event = $this->seatsioClient->events->create($chartKey);

        $report = $this->seatsioClient->eventReports->summaryByZone($event->key);

        $expectedReport = [
            'finishline' => [
                'count' => 2865,
                'byCategoryKey' => ["1" => 2865],
                'byCategoryLabel' => ["Goal Stands" => 2865],
                'byObjectType' => ["seat" => 2865],
                'bySection' => ["Goal Stand 3" => 2215, "Goal Stand 4" => 650],
                'byStatus' => ['free' => 2865],
                'byAvailability' => ['available' => 2865],
                'byAvailabilityReason' => ['available' => 2865],
                'bySelectability' => ['selectable' => 2865],
                'byChannel' => ['NO_CHANNEL' => 2865],
            ],
            'midtrack' => [
                'count' => 6032,
                'byCategoryKey' => ["2" => 6032],
                'byCategoryLabel' => ["Mid Track Stand" => 6032],
                'byObjectType' => ["seat" => 6032],
                'bySection' => ["MT1" => 2418, "MT3" => 3614],
                'byStatus' => ['free' => 6032],
                'byAvailability' => ['available' => 6032],
                'byAvailabilityReason' => ['available' => 6032],
                'bySelectability' => ['selectable' => 6032],
                'byChannel' => ['NO_CHANNEL' => 6032],
            ],
            'NO_ZONE' => [
                'count' => 0,
                'byCategoryKey' => [],
                'byCategoryLabel' => [],
                'byObjectType' => [],
                'bySection' => [],
                'byStatus' => [],
                'byAvailability' => [],
                'byAvailabilityReason' => [],
                'bySelectability' => [],
                'byChannel' => [],
            ]
        ];
        self::assertEquals($expectedReport, $report);
    }

    public function testSummaryByAvailability()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey);
        $this->seatsioClient->events->book($event->key, (new ObjectProperties("A-1")));

        $report = $this->seatsioClient->eventReports->summaryByAvailability($event->key);

        $expectedReport = [
            'available' => [
                'count' => 231,
                'byCategoryKey' => [9 => 115, 10 => 116],
                'byCategoryLabel' => ['Cat2' => 116, 'Cat1' => 115],
                'bySection' => ['NO_SECTION' => 231],
                'byZone' => ['NO_ZONE' => 231],
                'byStatus' => ['free' => 231],
                'byChannel' => ['NO_CHANNEL' => 231],
                'byObjectType' => ['seat' => 31, 'generalAdmission' => 200],
                'bySelectability' => ['selectable' => 231],
                'byAvailabilityReason' => ['available' => 231]
            ],
            'not_available' => [
                'count' => 1,
                'byCategoryKey' => [9 => 1],
                'byCategoryLabel' => ['Cat1' => 1],
                'bySection' => ['NO_SECTION' => 1],
                'byZone' => ['NO_ZONE' => 1],
                'byStatus' => ['booked' => 1],
                'byChannel' => ['NO_CHANNEL' => 1],
                'byObjectType' => ['seat' => 1],
                'bySelectability' => ['not_selectable' => 1],
                'byAvailabilityReason' => ['booked' => 1],
            ]
        ];
        self::assertEquals($expectedReport, $report);
    }

    public function testSummaryByAvailabilityReason()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey);
        $this->seatsioClient->events->book($event->key, (new ObjectProperties("A-1")));

        $report = $this->seatsioClient->eventReports->summaryByAvailabilityReason($event->key);

        $expectedReport = [
            'available' => [
                'count' => 231,
                'byCategoryKey' => [9 => 115, 10 => 116],
                'byCategoryLabel' => ['Cat2' => 116, 'Cat1' => 115],
                'bySection' => ['NO_SECTION' => 231],
                'byZone' => ['NO_ZONE' => 231],
                'byStatus' => ['free' => 231],
                'byChannel' => ['NO_CHANNEL' => 231],
                'byObjectType' => ['seat' => 31, 'generalAdmission' => 200],
                'bySelectability' => ['selectable' => 231],
                'byAvailability' => ['available' => 231]
            ],
            'booked' => [
                'count' => 1,
                'byCategoryKey' => [9 => 1],
                'byCategoryLabel' => ['Cat1' => 1],
                'bySection' => ['NO_SECTION' => 1],
                'byZone' => ['NO_ZONE' => 1],
                'byStatus' => ['booked' => 1],
                'byChannel' => ['NO_CHANNEL' => 1],
                'byObjectType' => ['seat' => 1],
                'bySelectability' => ['not_selectable' => 1],
                'byAvailability' => ['not_available' => 1]
            ],
            'reservedByToken' => [
                'count' => 0,
                'byCategoryKey' => [],
                'byCategoryLabel' => [],
                'bySection' => [],
                'byZone' => [],
                'byStatus' => [],
                'byChannel' => [],
                'byObjectType' => [],
                'bySelectability' => [],
                'byAvailability' => []
            ],
            'disabled_by_social_distancing' => [
                'count' => 0,
                'byCategoryKey' => [],
                'byCategoryLabel' => [],
                'bySection' => [],
                'byZone' => [],
                'byStatus' => [],
                'byChannel' => [],
                'byObjectType' => [],
                'bySelectability' => [],
                'byAvailability' => []
            ],
            'not_for_sale' => [
                'count' => 0,
                'byCategoryKey' => [],
                'byCategoryLabel' => [],
                'bySection' => [],
                'byZone' => [],
                'byStatus' => [],
                'byChannel' => [],
                'byObjectType' => [],
                'bySelectability' => [],
                'byAvailability' => []
            ]
        ];
        self::assertEquals($expectedReport, $report);
    }

    public function testSummaryByChannel()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey, (new CreateEventParams())->setChannels([
            new Channel("channel1", "channel 1", "#FF0000", 1, ["A-1", "A-2"])
        ]));

        $report = $this->seatsioClient->eventReports->summaryByChannel($event->key);

        $expectedReport = [
            'NO_CHANNEL' => [
                'count' => 230,
                'byCategoryKey' => [9 => 114, 10 => 116],
                'byCategoryLabel' => ['Cat2' => 116, 'Cat1' => 114],
                'bySection' => ['NO_SECTION' => 230],
                'byZone' => ['NO_ZONE' => 230],
                'byStatus' => ['free' => 230],
                'byAvailability' => ['available' => 230],
                'byAvailabilityReason' => ['available' => 230],
                'byObjectType' => ['seat' => 30, 'generalAdmission' => 200],
                'bySelectability' => ['selectable' => 230]
            ],
            'channel1' => [
                'count' => 2,
                'byCategoryKey' => [9 => 2],
                'byCategoryLabel' => ['Cat1' => 2],
                'bySection' => ['NO_SECTION' => 2],
                'byZone' => ['NO_ZONE' => 2],
                'byStatus' => ['free' => 2],
                'byAvailability' => ['available' => 2],
                'byAvailabilityReason' => ['available' => 2],
                'byObjectType' => ['seat' => 2],
                'bySelectability' => ['selectable' => 2]
            ]
        ];
        self::assertEquals($expectedReport, $report);
    }

}
