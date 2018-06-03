<?php

namespace Seatsio\Events;

use Seatsio\SeatsioClientTest;

class UpdateExtraDataTest extends SeatsioClientTest
{

    public function test()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey);
        $extraData = ["foo" => "bar"];

        $this->seatsioClient->events->updateExtraData($event->key, "A-1", $extraData);

        $objectStatus = $this->seatsioClient->events->retrieveObjectStatus($event->key, "A-1");
        self::assertEquals((object)$extraData, $objectStatus->extraData);
    }

}