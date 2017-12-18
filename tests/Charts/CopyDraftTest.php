<?php

namespace Seatsio\Charts;

use Seatsio\SeatsioClientTest;

class CopyDraftTest extends SeatsioClientTest
{

    public function test()
    {
        $chart = $this->seatsioClient->charts()->create('oldName');
        $this->seatsioClient->events()->create($chart->key);
        $this->seatsioClient->charts()->update($chart->key, 'newName');

        $copiedChart = $this->seatsioClient->charts()->copyDraft($chart->key);

        self::assertEquals('newName (copy)', $copiedChart->name);
        self::assertNotEquals($chart->key, $copiedChart->key);
    }

}