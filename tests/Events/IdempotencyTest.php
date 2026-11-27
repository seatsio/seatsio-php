<?php

namespace Seatsio\Events;

use Seatsio\FakeServer;
use Seatsio\Region;
use Seatsio\SeatsioClient;
use Seatsio\SeatsioClientTest;
use Seatsio\SeatsioException;

class IdempotencyTest extends SeatsioClientTest
{
    private const KEY = '3f0a7a41-7c5c-4a43-9b0e-0d6c2a5f8e11';

    public function testSameKeyReplaysTheResponse()
    {
        $this->useTestCompanyWithIdempotencyEnabled();
        $eventKey = $this->createEventWithoutIdempotencyKey();
        $client = $this->seatsioClient->withIdempotencyKey(self::KEY);

        $first = $client->events->book($eventKey, ['A-1']);
        $this->releaseWithoutIdempotencyKey($eventKey, 'A-1');
        $second = $client->events->book($eventKey, ['A-1']);

        self::assertEquals(['A-1'], array_keys($first->objects));
        self::assertEquals(array_keys($first->objects), array_keys($second->objects));
        self::assertEquals(EventObjectInfo::$FREE, $this->seatsioClient->events->retrieveObjectInfo($eventKey, 'A-1')->status);
    }

    public function testSameKeyWithADifferentRequestIsRejected()
    {
        $this->useTestCompanyWithIdempotencyEnabled();
        $eventKey = $this->createEventWithoutIdempotencyKey();
        $client = $this->seatsioClient->withIdempotencyKey(self::KEY);
        $client->events->book($eventKey, ['A-1']);

        try {
            $client->events->book($eventKey, ['A-2']);
            self::fail('expected exception');
        } catch (SeatsioException $e) {
            self::assertEquals('IDEMPOTENCY_KEY_REUSED', $e->errors[0]->code);
        }
    }

    public function testKeyIsIgnoredWhenIdempotencyIsNotEnabledForTheCompany()
    {
        $eventKey = $this->createEventWithoutIdempotencyKey();
        $client = $this->seatsioClient->withIdempotencyKey(self::KEY);

        $client->events->book($eventKey, ['A-1']);
        $this->seatsioClient->events->release($eventKey, ['A-1']);
        $client->events->book($eventKey, ['A-1']);

        self::assertEquals(EventObjectInfo::$BOOKED, $this->seatsioClient->events->retrieveObjectInfo($eventKey, 'A-1')->status);
    }

    private function createEventWithoutIdempotencyKey(): string
    {
        return $this->seatsioClient->events->create($this->createTestChart())->key;
    }

    private function releaseWithoutIdempotencyKey(string $eventKey, string $object): void
    {
        $this->seatsioClient->events->release($eventKey, [$object]);
    }

    public function testSuppliedKeyIsAvailableOnAFailedRequest()
    {
        $this->useTestCompanyWithIdempotencyEnabled();

        try {
            $this->seatsioClient->withIdempotencyKey(self::KEY)->events->book('unknownEvent', ['A-1']);
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
            $this->seatsioClient->events->book('unknownEvent', ['A-1']);
            self::fail('expected exception');
        } catch (SeatsioException $e) {
            self::assertNotEmpty($e->idempotencyKey);
        }
    }

    public function testRetryAfterALostResponseBooksOnce()
    {
        $this->useTestCompanyWithIdempotencyEnabled();
        $eventKey = $this->createEventWithoutIdempotencyKey();
        $proxy = FakeServer::replying(FakeServer::proxyTo(self::baseUrl()), FakeServer::proxyTo(self::baseUrl(), true), FakeServer::proxyTo(self::baseUrl()));
        $client = new SeatsioClient(Region::withUrl($proxy->url()), $this->user->secretKey);

        try {
            $client->events->book($eventKey, ['A-1']);
            $result = $client->events->book($eventKey, ['A-2']);

            self::assertEquals(['A-2'], array_keys($result->objects));
            self::assertCount(3, $proxy->requests());
            self::assertEquals($proxy->sentIdempotencyKeys()[1], $proxy->sentIdempotencyKeys()[2]);
            self::assertEquals(EventObjectInfo::$BOOKED, $this->seatsioClient->events->retrieveObjectInfo($eventKey, 'A-2')->status);
        } finally {
            $proxy->close();
        }
    }

    public function testTheFirstCallToARouteIsNotRetriedAfterALostResponse()
    {
        $this->useTestCompanyWithIdempotencyEnabled();
        $eventKey = $this->createEventWithoutIdempotencyKey();
        $proxy = FakeServer::replying(FakeServer::proxyTo(self::baseUrl(), true), FakeServer::proxyTo(self::baseUrl()));
        $client = new SeatsioClient(Region::withUrl($proxy->url()), $this->user->secretKey);

        try {
            $client->events->book($eventKey, ['A-1']);
            self::fail('expected exception');
        } catch (SeatsioException $e) {
            self::assertTrue($e->outcomeIsUnknown());
            self::assertFalse($e->isSafeToRetryWithSameKey());
            self::assertCount(1, $proxy->requests());
            self::assertEquals(EventObjectInfo::$BOOKED, $this->seatsioClient->events->retrieveObjectInfo($eventKey, 'A-1')->status);
        } finally {
            $proxy->close();
        }
    }
}
