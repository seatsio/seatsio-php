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

        $objectInfo1 = $this->seatsioClient->events->retrieveObjectInfo($event->key, "A-1");
        self::assertEquals((object)$extraData1, $objectInfo1->extraData);
        $objectInfo2 = $this->seatsioClient->events->retrieveObjectInfo($event->key, "A-2");
        self::assertEquals((object)$extraData2, $objectInfo2->extraData);
    }

}
