<?php

namespace Seatsio\Subaccounts;


use Seatsio\SeatsioClientTest;

class CopyChartToParentTest extends SeatsioClientTest
{

    public function test()
    {
        $subaccount = $this->seatsioClient->subaccounts()->create();
        $chart = self::createSeatsioClient($subaccount->secretKey)->charts()->create('aChart');

        $copiedChart = $this->seatsioClient->subaccounts()->copyChartToParent($subaccount->id, $chart->key);
        self::assertEquals($copiedChart->name, 'aChart');
        $retrievedChart = $this->seatsioClient->charts()->retrieve($copiedChart->key);
        self::assertEquals($retrievedChart->name, 'aChart');
    }
}