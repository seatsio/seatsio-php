<?php

namespace Seatsio\Charts;

use Seatsio\SeatsioClientTest;

class CopyChartToSubaccountTest extends SeatsioClientTest
{

    public function test()
    {
        $chart = $this->seatsioClient->charts()->create('my chart');
        $subaccount = $this->seatsioClient->subaccounts()->create();
        $subaccountSeatsioClient = self::createSeatsioClient($subaccount->secretKey);

        $copiedChart = $this->seatsioClient->charts()->copyToSubaccount($chart->key, $subaccount->id);

        self::assertEquals('my chart', $copiedChart->name);
        self::assertNotEquals($chart->key, $copiedChart->key);
        $retrievedChart = $subaccountSeatsioClient->charts()->retrieve($copiedChart->key);
        self::assertEquals('my chart', $retrievedChart->name);
    }

}