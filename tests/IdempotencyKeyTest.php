<?php

namespace Seatsio;

use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class IdempotencyKeyTest extends TestCase
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

    private static function assertV4Uuid($key): void
    {
        self::assertMatchesRegularExpression('/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/', $key);
    }

    public static function writeMethods(): array
    {
        return [['POST'], ['PUT'], ['DELETE'], ['PATCH']];
    }

    #[DataProvider('writeMethods')]
    public function testWritesGetAnIdempotencyKey(string $method)
    {
        $client = $this->client(FakeServer::replying(FakeServer::status(204)));

        $client->client->request($method, '/anything', ['body' => '{}']);

        self::assertV4Uuid($this->server->sentIdempotencyKeys()[0]);
    }

    public function testGetsDoNotGetAnIdempotencyKey()
    {
        $client = $this->client(FakeServer::replying(FakeServer::status(204)));

        $client->client->get('/anything');

        self::assertNull($this->server->sentIdempotencyKeys()[0]);
    }

    public function testEachRequestGetsItsOwnKey()
    {
        $client = $this->client(FakeServer::replying(FakeServer::status(204)));

        $client->client->post('/anything');
        $client->client->post('/anything');

        $keys = $this->server->sentIdempotencyKeys();
        self::assertNotEquals($keys[0], $keys[1]);
    }

    public function testClientWithKeyReusesIt()
    {
        $key = '3f0a7a41-7c5c-4a43-9b0e-0d6c2a5f8e11';
        $client = $this->client(FakeServer::replying(FakeServer::status(204)))->withIdempotencyKey($key);

        $client->client->post('/anything');
        $client->client->delete('/anything');

        self::assertEquals([$key, $key], $this->server->sentIdempotencyKeys());
    }

    public function testKeepsAKeyTheRequestAlreadyHas()
    {
        $key = '3f0a7a41-7c5c-4a43-9b0e-0d6c2a5f8e11';
        $client = $this->client(FakeServer::replying(FakeServer::status(204)));

        $client->client->post('/anything', ['headers' => ['Idempotency-Key' => $key]]);

        self::assertEquals([$key], $this->server->sentIdempotencyKeys());
    }

    public function testKeyIsCaseInsensitive()
    {
        $key = strtoupper('3f0a7a41-7c5c-4a43-9b0e-0d6c2a5f8e11');
        $client = $this->client(FakeServer::replying(FakeServer::status(204)))->withIdempotencyKey($key);

        $client->client->post('/anything');

        self::assertEquals([$key], $this->server->sentIdempotencyKeys());
    }

    public static function invalidKeys(): array
    {
        return [[''], ['notAUuid'], ['f81d4fae-7dec-11d0-a765-00a0c91e6bf6'], ['00000000-0000-0000-0000-000000000000'], ['1-1-1-1-1'], ["3f0a7a41-7c5c-4a43-9b0e-0d6c2a5f8e11\n"]];
    }

    #[DataProvider('invalidKeys')]
    public function testSuppliedKeyMustBeAV4Uuid(string $key)
    {
        $this->expectException(InvalidArgumentException::class);

        (new SeatsioClient(Region::withUrl('http://localhost:1'), 'secretKey'))->withIdempotencyKey($key);
    }

    public function testClientWithKeyIsAFullClient()
    {
        $client = new SeatsioClient(Region::withUrl('http://localhost:1'), 'secretKey', 'workspaceKey');
        $copy = $client->withIdempotencyKey('3f0a7a41-7c5c-4a43-9b0e-0d6c2a5f8e11');

        self::assertNotSame($client->client, $copy->client);
        self::assertNotNull($copy->events);
        self::assertNotNull($copy->charts);
    }

    public function testErrorBodyWithEmptyErrorsKeepsMessagesAndRequestId()
    {
        $client = $this->client(FakeServer::replying(FakeServer::json(400, '{"errors": [], "messages": ["went wrong"], "requestId": "req-9"}')), 0);

        try {
            $client->client->post('/anything', ['body' => '{}']);
            self::fail('expected exception');
        } catch (SeatsioException $e) {
            self::assertEquals('went wrong', $e->getMessage());
            self::assertEquals('req-9', $e->requestId);
        }
    }

    public function testClientWithKeySendsTheSecretKeyAndWorkspaceKey()
    {
        $this->server = FakeServer::replying(FakeServer::status(204));
        $client = (new SeatsioClient(Region::withUrl($this->server->url()), 'secretKey', 'workspaceKey'))
            ->withIdempotencyKey('3f0a7a41-7c5c-4a43-9b0e-0d6c2a5f8e11');

        $client->client->post('/anything');

        $headers = $this->server->requests()[0]['headers'];
        self::assertEquals('Basic ' . base64_encode('secretKey:'), $headers['authorization']);
        self::assertEquals('workspaceKey', $headers['x-workspace-key']);
    }

    public static function errorResponses(): array
    {
        return [
            'known error' => [FakeServer::error(400, 'EVENT_NOT_FOUND')],
            'json without errors' => [FakeServer::json(500, '{}')],
            'empty errors' => [FakeServer::json(500, '{"errors": []}')],
            'malformed' => [FakeServer::json(500, 'malformed')],
            'no body' => [FakeServer::status(500)],
            'no content type' => [['status' => 500, 'headers' => [], 'body' => '{}']],
        ];
    }

    #[DataProvider('errorResponses')]
    public function testErrorResponsesExposeTheKey(array $reply)
    {
        $client = $this->client(FakeServer::replying($reply), 0);

        try {
            $client->client->post('/anything', ['body' => '{}']);
            self::fail('expected exception');
        } catch (SeatsioException $e) {
            self::assertEquals($this->server->sentIdempotencyKeys()[0], $e->idempotencyKey);
        }
    }

    #[DataProvider('errorResponses')]
    public function testErrorResponsesOfGetsHaveNoKey(array $reply)
    {
        $client = $this->client(FakeServer::replying($reply), 0);

        try {
            $client->client->get('/anything');
            self::fail('expected exception');
        } catch (SeatsioException $e) {
            self::assertNull($e->idempotencyKey);
        }
    }

    public function testConnectionFailuresExposeTheKey()
    {
        $client = new SeatsioClient(Region::withUrl('http://localhost:1'), 'secretKey');

        try {
            $client->client->post('/anything', ['body' => '{}']);
            self::fail('expected exception');
        } catch (SeatsioException $e) {
            self::assertV4Uuid($e->idempotencyKey);
            self::assertTrue($e->outcomeIsUnknown());
        }
    }

    public function testConnectionFailuresOfGetsHaveNoKey()
    {
        $client = new SeatsioClient(Region::withUrl('http://localhost:1'), 'secretKey');

        try {
            $client->client->get('/anything');
            self::fail('expected exception');
        } catch (SeatsioException $e) {
            self::assertNull($e->idempotencyKey);
        }
    }

    public static function bodiesWithoutAnErrorCode(): array
    {
        return [['{}'], ['{"errors": []}'], ['{"errors": null}'], ['null'], ['malformed'], ['[]'], ['{"errors": [null]}']];
    }

    #[DataProvider('bodiesWithoutAnErrorCode')]
    public function testJsonErrorResponseWithoutAnErrorCodeGivesAGenericException(string $body)
    {
        $client = $this->client(FakeServer::replying(FakeServer::json(500, $body)), 0);

        try {
            $client->client->get('/anything');
            self::fail('expected exception');
        } catch (SeatsioException $e) {
            self::assertEquals('GET ' . $this->server->url('/anything') . ' resulted in a `500 Fake` response. Body: ' . $body, $e->getMessage());
            self::assertEmpty($e->errors);
            self::assertNull($e->requestId);
        }
    }

    public function testRateLimitWithoutAnErrorCodeStillGivesARateLimitExceededException()
    {
        $client = $this->client(FakeServer::replying(FakeServer::json(429, '{}')), 0);

        $this->expectException(RateLimitExceededException::class);
        $client->client->get('/anything');
    }

    public function testBestAvailableObjectsNotFoundIsStillRecognised()
    {
        $client = $this->client(FakeServer::replying(FakeServer::error(400, 'BEST_AVAILABLE_OBJECTS_NOT_FOUND')), 0);

        $this->expectException(BestAvailableObjectsNotFoundException::class);
        $client->client->post('/anything');
    }
}
