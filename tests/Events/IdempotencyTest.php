<?php

namespace Seatsio\Events;

use Seatsio\SeatsioClientTest;
use Seatsio\SeatsioException;

class IdempotencyTest extends SeatsioClientTest
{
    private const KEY = '3f0a7a41-7c5c-4a43-9b0e-0d6c2a5f8e11';

    public function testSameKeyReplaysTheResponse()
    {
        $this->useTestCompanyWithIdempotencyEnabled();
        $chartKey = $this->createTestChart();
        $client = $this->seatsioClient->withIdempotencyKey(self::KEY);

        $first = $client->events->create($chartKey);
        $second = $client->events->create($chartKey);

        self::assertEquals($first->id, $second->id);
        self::assertEquals($first->key, $second->key);
    }

    public function testSameKeyWithADifferentRequestIsRejected()
    {
        $this->useTestCompanyWithIdempotencyEnabled();
        $chartKey = $this->createTestChart();
        $client = $this->seatsioClient->withIdempotencyKey(self::KEY);
        $client->events->create($chartKey);

        try {
            $client->events->create($chartKey, (new CreateEventParams())->setKey('anotherEvent'));
            self::fail('expected exception');
        } catch (SeatsioException $e) {
            self::assertEquals('IDEMPOTENCY_KEY_REUSED', $e->errors[0]->code);
        }
    }

    public function testKeyIsIgnoredWhenIdempotencyIsNotEnabledForTheCompany()
    {
        $chartKey = $this->createTestChart();
        $client = $this->seatsioClient->withIdempotencyKey(self::KEY);

        $first = $client->events->create($chartKey);
        $second = $client->events->create($chartKey);

        self::assertNotEquals($first->id, $second->id);
    }

    public function testSuppliedKeyIsAvailableOnAFailedRequest()
    {
        $this->useTestCompanyWithIdempotencyEnabled();

        try {
            $this->seatsioClient->withIdempotencyKey(self::KEY)->events->create('unknownChart');
            self::fail('expected exception');
        } catch (SeatsioException $e) {
            self::assertEquals(self::KEY, $e->idempotencyKey);
            self::assertFalse($e->outcomeIsUnknown());
        }
    }

    public function testGeneratedKeyIsAvailableOnAFailedRequest()
    {
        $this->useTestCompanyWithIdempotencyEnabled();

        try {
            $this->seatsioClient->events->create('unknownChart');
            self::fail('expected exception');
        } catch (SeatsioException $e) {
            self::assertNotEmpty($e->idempotencyKey);
        }
    }
}
