<?php

namespace Seatsio;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class IdempotentRetryTest extends TestCase
{
    private ?FakeServer $server = null;

    protected function tearDown(): void
    {
        $this->server?->close();
    }

    private function client(FakeServer $server, int $maxRetries = 5): SeatsioClient
    {
        $this->server = $server;
        return new SeatsioClient(Region::withUrl($server->url()), 'secretKey', null, $maxRetries);
    }

    public static function writeMethods(): array
    {
        return [['POST'], ['PUT'], ['DELETE']];
    }

    #[DataProvider('writeMethods')]
    public function testWritesAreNotRetriedByDefaultWhenTheConnectionFails(string $method)
    {
        $client = $this->client(FakeServer::replying(FakeServer::drop(), FakeServer::status(204)));

        try {
            $client->client->request($method, '/anything', ['body' => '{}']);
            self::fail('expected exception');
        } catch (SeatsioException $e) {
            self::assertCount(1, $this->server->requests());
            self::assertTrue($e->outcomeIsUnknown());
        }
    }

    #[DataProvider('writeMethods')]
    public function testWritesAreRetriedWithTheSameKeyWhenEnabled(string $method)
    {
        $client = $this->client(FakeServer::replying(FakeServer::drop(), FakeServer::status(204)))->retryNonIdempotentRequests(true);

        $response = $client->client->request($method, '/anything', ['body' => '{}']);

        self::assertEquals(204, $response->getStatusCode());
        $keys = $this->server->sentIdempotencyKeys();
        self::assertCount(2, $keys);
        self::assertNotNull($keys[0]);
        self::assertEquals($keys[0], $keys[1]);
        self::assertEquals('{}', $this->server->requests()[1]['body']);
    }

    public function testWritesAreNotRetriedAfterRetriesAreDisabledAgain()
    {
        $client = $this->client(FakeServer::replying(FakeServer::drop(), FakeServer::status(204)))
            ->retryNonIdempotentRequests(true)
            ->retryNonIdempotentRequests(false);

        $this->expectException(SeatsioException::class);
        try {
            $client->client->post('/anything');
        } finally {
            self::assertCount(1, $this->server->requests());
        }
    }

    public function testConnectionFailureRetriesStopAfterMaxRetries()
    {
        $client = $this->client(FakeServer::replying(FakeServer::drop()), 2)->retryNonIdempotentRequests(true);

        try {
            $client->client->post('/anything');
            self::fail('expected exception');
        } catch (SeatsioException $e) {
            self::assertCount(2, $this->server->requests());
            self::assertTrue($e->outcomeIsUnknown());
        }
    }

    public function testGetsAreNotRetriedOnConnectionFailures()
    {
        $client = $this->client(FakeServer::replying(FakeServer::drop(), FakeServer::status(204)))->retryNonIdempotentRequests(true);

        try {
            $client->client->get('/anything');
            self::fail('expected exception');
        } catch (SeatsioException $e) {
            self::assertCount(1, $this->server->requests());
        }
    }

    public function testConnectionRefusedIsRetriedWhenEnabled()
    {
        $refused = Region::withUrl('http://127.0.0.1:1');
        $notRetried = microtime(true);
        $this->expectConnectionFailure(new SeatsioClient($refused, 'secretKey', null, 2));
        $notRetried = microtime(true) - $notRetried;

        $retried = microtime(true);
        $this->expectConnectionFailure((new SeatsioClient($refused, 'secretKey', null, 2))->retryNonIdempotentRequests(true));
        $retried = microtime(true) - $retried;

        self::assertGreaterThan($notRetried + 0.3, $retried);
    }

    private function expectConnectionFailure(SeatsioClient $client): void
    {
        try {
            $client->client->post('/anything');
            self::fail('expected exception');
        } catch (SeatsioException $e) {
            self::assertTrue($e->outcomeIsUnknown());
        }
    }

    public function testWritesAreRetriedOn429WithTheSameKey()
    {
        $client = $this->client(FakeServer::replying(FakeServer::error(429, 'RATE_LIMIT_EXCEEDED'), FakeServer::status(204)));

        $response = $client->client->post('/anything', ['body' => '{}']);

        self::assertEquals(204, $response->getStatusCode());
        $keys = $this->server->sentIdempotencyKeys();
        self::assertCount(2, $keys);
        self::assertEquals($keys[0], $keys[1]);
    }

    public function testWritesAreRetriedOnKeyInUseWithTheSameKey()
    {
        $client = $this->client(FakeServer::replying(FakeServer::error(409, 'IDEMPOTENCY_KEY_IN_USE'), FakeServer::status(204)));

        $response = $client->client->post('/anything', ['body' => '{}']);

        self::assertEquals(204, $response->getStatusCode());
        $keys = $this->server->sentIdempotencyKeys();
        self::assertCount(2, $keys);
        self::assertEquals($keys[0], $keys[1]);
    }

    public function testCallerKeySurvivesARetry()
    {
        $key = '3f0a7a41-7c5c-4a43-9b0e-0d6c2a5f8e11';
        $client = $this->client(FakeServer::replying(FakeServer::error(409, 'IDEMPOTENCY_KEY_IN_USE'), FakeServer::status(204)))->withIdempotencyKey($key);

        $client->client->post('/anything');

        self::assertEquals([$key, $key], $this->server->sentIdempotencyKeys());
    }

    public function testKeyInUseErrorIsThrownWhenRetriesRunOut()
    {
        $client = $this->client(FakeServer::replying(FakeServer::error(409, 'IDEMPOTENCY_KEY_IN_USE')), 2);

        try {
            $client->client->post('/anything');
            self::fail('expected exception');
        } catch (SeatsioException $e) {
            self::assertCount(2, $this->server->requests());
            self::assertEquals('IDEMPOTENCY_KEY_IN_USE', $e->errors[0]->code);
            self::assertTrue($e->outcomeIsUnknown());
        }
    }

    public static function otherConflicts(): array
    {
        return [
            ['{"errors": [{"code": "IDEMPOTENCY_KEY_REUSED", "message": "m"}]}'],
            ['{"errors": [{"code": "SOMETHING_ELSE", "message": "m"}]}'],
            ['{}'],
            ['malformed'],
        ];
    }

    #[DataProvider('otherConflicts')]
    public function testOtherConflictsAreNotRetried(string $body)
    {
        $client = $this->client(FakeServer::replying(FakeServer::json(409, $body), FakeServer::status(204)));

        try {
            $client->client->post('/anything');
            self::fail('expected exception');
        } catch (SeatsioException $e) {
            self::assertCount(1, $this->server->requests());
            self::assertFalse($e->outcomeIsUnknown());
        }
    }

    public function testRetrySettingsAreSharedWithClientsThatHaveAKey()
    {
        $client = $this->client(FakeServer::replying(FakeServer::drop(), FakeServer::status(204)));
        $withKey = $client->withIdempotencyKey('3f0a7a41-7c5c-4a43-9b0e-0d6c2a5f8e11');

        $client->retryNonIdempotentRequests(true);

        self::assertEquals(204, $withKey->client->post('/anything')->getStatusCode());
        self::assertCount(2, $this->server->requests());
    }

    public function testMaxRetriesIsSharedWithClientsThatHaveAKey()
    {
        $client = $this->client(FakeServer::replying(FakeServer::error(429, 'RATE_LIMIT_EXCEEDED')), 1);
        $withKey = $client->withIdempotencyKey('3f0a7a41-7c5c-4a43-9b0e-0d6c2a5f8e11');

        try {
            $withKey->client->post('/anything');
            self::fail('expected exception');
        } catch (RateLimitExceededException $e) {
            self::assertCount(1, $this->server->requests());
        }
    }

    public static function serverErrors(): array
    {
        return [[500], [502], [503], [504]];
    }

    #[DataProvider('serverErrors')]
    public function testOutcomeIsUnknownAfterA5xx(int $status)
    {
        $client = $this->client(FakeServer::replying(FakeServer::error($status, 'SERVER_ERROR')));

        try {
            $client->client->post('/anything');
            self::fail('expected exception');
        } catch (SeatsioException $e) {
            self::assertTrue($e->outcomeIsUnknown());
        }
    }

    public static function clientErrors(): array
    {
        return [[400], [401], [404], [422], [429]];
    }

    #[DataProvider('clientErrors')]
    public function testOutcomeIsKnownAfterA4xx(int $status)
    {
        $client = $this->client(FakeServer::replying(FakeServer::error($status, 'CLIENT_ERROR')), 0);

        try {
            $client->client->post('/anything');
            self::fail('expected exception');
        } catch (SeatsioException $e) {
            self::assertFalse($e->outcomeIsUnknown());
        }
    }

    public function testAnOperationWithUnknownOutcomeCanBeRetriedWithTheKeyOfTheException()
    {
        $client = $this->client(FakeServer::replying(FakeServer::drop(), FakeServer::status(204)));

        try {
            $client->client->post('/anything', ['body' => '{}']);
            self::fail('expected exception');
        } catch (SeatsioException $e) {
            self::assertTrue($e->outcomeIsUnknown());
            $client->withIdempotencyKey($e->idempotencyKey)->client->post('/anything', ['body' => '{}']);
        }

        $keys = $this->server->sentIdempotencyKeys();
        self::assertCount(2, $keys);
        self::assertEquals($keys[0], $keys[1]);
    }
}
