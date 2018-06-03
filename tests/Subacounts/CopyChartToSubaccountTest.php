<?php

namespace Seatsio\Subaccounts;


use Seatsio\SeatsioClientTest;

class CopyChartToSubaccountTest extends SeatsioClientTest
{

    public function test()
    {
        $fromSubaccount = $this->seatsioClient->subaccounts->create();
        $toSubaccount = $this->seatsioClient->subaccounts->create();
        $chart = self::createSeatsioClient($fromSubaccount->secretKey)->charts->create('aChart');

        $copiedChart = $this->seatsioClient->subaccounts->copyChartToSubaccount($fromSubaccount->id, $toSubaccount->id, $chart->key);
        self::assertEquals($copiedChart->name, 'aChart');
        $retrievedChart = self::createSeatsioClient($toSubaccount->secretKey)->charts->retrieve($copiedChart->key);
        self::assertEquals($retrievedChart->name, 'aChart');
    }
}