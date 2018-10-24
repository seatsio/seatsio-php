<?php

namespace Seatsio\Events;

use Seatsio\SeatsioClientTest;

class UpdateEventTest extends SeatsioClientTest
{

    public function testUpdateChartKey()
    {
        $chart1 = $this->seatsioClient->charts->create();
        $chart2 = $this->seatsioClient->charts->create();
        $event = $this->seatsioClient->events->create($chart1->key);

        $this->seatsioClient->events->update($event->key, $chart2->key);

        $retrievedEvent = $this->seatsioClient->events->retrieve($event->key);
        self::assertEquals($chart2->key, $retrievedEvent->chartKey);
        self::assertNotNull($retrievedEvent->updatedOn);
    }

    public function testUpdateEventKey()
    {
        $chart = $this->seatsioClient->charts->create();
        $event = $this->seatsioClient->events->create($chart->key);

        $this->seatsioClient->events->update($event->key, null, 'newKey');

        $retrievedEvent = $this->seatsioClient->events->retrieve('newKey');
        self::assertEquals($chart->key, $retrievedEvent->chartKey);
        self::assertEquals('newKey', $retrievedEvent->key);
    }

    public function testUpdateBookWholeTables()
    {
        $chart = $this->seatsioClient->charts->create();
        $event = $this->seatsioClient->events->create($chart->key);

        $this->seatsioClient->events->update($event->key, null, null, true);

        $retrievedEvent = $this->seatsioClient->events->retrieve($event->key);
        self::assertEquals($chart->key, $retrievedEvent->chartKey);
        self::assertEquals($event->key, $retrievedEvent->key);
        self::assertTrue($retrievedEvent->bookWholeTables);
    }

    public function testUpdateTableBookingModes()
    {
        $chartKey = $this->createTestChartWithTables();
        $event = $this->seatsioClient->events->create($chartKey);

        $this->seatsioClient->events->update($event->key, null, null, ["T1" => "BY_TABLE", "T2" => "BY_SEAT"]);

        $retrievedEvent = $this->seatsioClient->events->retrieve($event->key);
        self::assertEquals($chartKey, $retrievedEvent->chartKey);
        self::assertEquals($event->key, $retrievedEvent->key);
        self::assertFalse($retrievedEvent->bookWholeTables);
        self::assertEquals((object)["T1" => "BY_TABLE", "T2" => "BY_SEAT"], $retrievedEvent->tableBookingModes);
    }

}