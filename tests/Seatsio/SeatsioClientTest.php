<?php

namespace Seatsio;

class SeatsioClientTest extends SeatsioTest
{

    public function testGetCharts()
    {

        $this->httpClient->registerResponseOnGet('/charts', 200, '[ {"data": "foo" } ]');

        $seatsioClient = new SeatsioClient($this->httpClient);

        $charts = $seatsioClient->charts->all();

        $this->assertEquals(1, sizeof($charts));
        $this->assertHttpClientCalled("/charts");
    }


}
