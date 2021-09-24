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

        $objectInfo = $this->seatsioClient->events->retrieveObjectInfo($event->key, "A-1");
        self::assertEquals((object)$extraData, $objectInfo->extraData);
    }

}
