<?php

namespace Seatsio\Events;

use Seatsio\SeatsioClientTest;

class UpdateExtraDatasTest extends SeatsioClientTest
{

    public function test()
    {
        $chartKey = $this->createTestChart();
        $event = $this->seatsioClient->events->create($chartKey);
        $extraData1 = ["foo1" => "bar1"];
        $extraData2 = ["foo2" => "bar2"];

        $this->seatsioClient->events->updateExtraDatas($event->key, ["A-1" => $extraData1, "A-2" => $extraData2]);

        $objectStatus1 = $this->seatsioClient->events->retrieveObjectInfo($event->key, "A-1");
        self::assertEquals((object)$extraData1, $objectStatus1->extraData);
        $objectStatus2 = $this->seatsioClient->events->retrieveObjectInfo($event->key, "A-2");
        self::assertEquals((object)$extraData2, $objectStatus2->extraData);
    }

}
