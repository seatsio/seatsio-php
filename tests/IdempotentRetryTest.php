<?php

namespace Seatsio;

use GuzzleHttp\Exception\ConnectException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Seatsio\Http\SharedState;

class IdempotentRetryTest extends TestCase
{
    private const BOOK = '/events/{key}/actions/book';
    private const KEY = '3f0a7a41-7c5c-4a43-9b0e-0d6c2a5f8e11';

    private FakeClock $clock;
    private ?FakeServer $server = null;

    protected function setUp(): void
    {
        $this->clock = new FakeClock();
    }

    protected function tearDown(): void
    {
        $this->server?->close();
    }

    private function serve(array ...$replies): SeatsioClient
    {
        $this->server = FakeServer::replying(...$replies);
        return $this->clientFor($this->server->url());
    }

    private function clientFor(string $url, string $secretKey = 'secretKey', ?string $workspaceKey = null): SeatsioClient
    {
        $client = new SeatsioClient(Region::withUrl($url), $secretKey, $workspaceKey);
        self::state($client)->clock = $this->clock;
        return $client;
    }

    private function clientOnFreePort(): array
    {
        $port = FakeServer::freePort();
        return [$port, $this->clientFor('http://127.0.0.1:' . $port)];
    }

    private function startServerOn(int $port, array ...$replies): void
    {
        $this->server = FakeServer::replyingOn($port, ...$replies);
    }

    private static function state(SeatsioClient $client): SharedState
    {
        return (fn() => $this->state)->call($client);
    }

    private static function maxRetries(SeatsioClient $client, int $maxRetries): SeatsioClient
    {
        self::state($client)->maxRetries = $maxRetries;
        return $client;
    }

    private static function book(SeatsioClient $client, string $eventKey = 'event1', string $method = 'POST', array $options = [])
    {
        return $client->client->request($method, self::BOOK, ['path_params' => ['key' => $eventKey], 'body' => '{}'] + $options);
    }

    private static function failure(callable $call): SeatsioException
    {
        try {
            $call();
        } catch (SeatsioException $e) {
            return $e;
        }
        self::fail('expected a SeatsioException');
    }

    private static function keyStatus(int $status, string $keyStatus, string $body = '{}'): array
    {
        return FakeServer::keyStatus(FakeServer::json($status, $body), $keyStatus);
    }

    private static function keyStatusError(int $status, string $code, string $keyStatus): array
    {
        return FakeServer::keyStatus(FakeServer::error($status, $code), $keyStatus);
    }

    private function keys(): array
    {
        return $this->server->sentIdempotencyKeys();
    }

    public static function retriedMethods(): array
    {
        return [['POST'], ['DELETE']];
    }

    #[DataProvider('retriedMethods')]
    public function testALostResponseIsRetriedWithTheSameKeyOnARouteThatHonorsKeys(string $method)
    {
        $client = $this->serve(self::keyStatus(200, 'executed'), FakeServer::drop(), self::keyStatus(200, 'replayed', '{"replayed": true}'));

        self::book($client, 'event1', $method);
        $response = self::book($client, 'event1', $method);

        self::assertEquals('{"replayed": true}', (string)$response->getBody());
        self::assertEquals(3, $this->server->requestCount());
        self::assertEquals($this->keys()[1], $this->keys()[2]);
        self::assertNotEquals($this->keys()[0], $this->keys()[1]);
    }

    public function testALostResponseIsNotRetriedOnARouteThatIgnoresKeys()
    {
        $client = $this->serve(self::keyStatus(200, 'ignored'), FakeServer::drop());

        self::book($client);
        $e = self::failure(fn() => self::book($client));

        self::assertEquals(2, $this->server->requestCount());
        self::assertTrue($e->outcomeIsUnknown());
        self::assertEquals($this->keys()[1], $e->idempotencyKey);
        self::assertFalse($e->isSafeToRetryWithSameKey());
    }

    public function testALostResponseIsNotRetriedOnARouteThatNeverSentAStatus()
    {
        $client = $this->serve(FakeServer::status(200), FakeServer::drop());

        self::book($client);
        $e = self::failure(fn() => self::book($client));

        self::assertEquals(2, $this->server->requestCount());
        self::assertTrue($e->outcomeIsUnknown());
        self::assertFalse($e->isSafeToRetryWithSameKey());
    }

    public static function writeMethods(): array
    {
        return [['POST'], ['PUT'], ['DELETE'], ['PATCH']];
    }

    #[DataProvider('writeMethods')]
    public function testTheFirstRequestToARouteIsNotRetriedAfterALostResponse(string $method)
    {
        $client = $this->serve(FakeServer::drop(), FakeServer::status(200));

        $e = self::failure(fn() => self::book($client, 'event1', $method));

        self::assertEquals(1, $this->server->requestCount());
        self::assertTrue($e->outcomeIsUnknown());
        self::assertFalse($e->isSafeToRetryWithSameKey());
        self::assertEquals($this->keys()[0], $e->idempotencyKey);
    }

    public function testARequestThatWasNeverSentIsRetriedWithTheSameKey()
    {
        [$port, $client] = $this->clientOnFreePort();
        $this->clock->onSleep = fn() => $this->startServerOn($port, FakeServer::status(200));

        self::book($client->withIdempotencyKey(self::KEY));

        self::assertEquals(1, $this->clock->sleeps);
        self::assertEquals([self::KEY], $this->keys());
    }

    public function testARequestThatWasNeverSentIsSafeToRetryWithTheSameKey()
    {
        [, $client] = $this->clientOnFreePort();

        $e = self::failure(fn() => self::book(self::maxRetries($client, 0)));

        self::assertFalse($e->outcomeIsUnknown());
        self::assertTrue($e->isSafeToRetryWithSameKey());
        self::assertNotNull($e->idempotencyKey);
        self::assertNotNull($e->getPrevious());
    }

    public function testALostResponseIsSafeToRetryWithTheSameKeyOnARouteThatHonorsKeys()
    {
        $client = $this->serve(self::keyStatus(200, 'executed'), FakeServer::drop());

        self::book($client);
        $e = self::failure(fn() => self::book(self::maxRetries($client, 0)));

        self::assertTrue($e->outcomeIsUnknown());
        self::assertTrue($e->isSafeToRetryWithSameKey());
    }

    public function testPathParamsAreEncoded()
    {
        $client = $this->serve(FakeServer::status(200));

        self::book($client, 'a b/c?d#é');

        self::assertEquals('/events/a%20b%2Fc%3Fd%23%C3%A9/actions/book', $this->server->requests()[0]['path']);
    }

    public function testPathsWithEncodedParamsShareTheMemory()
    {
        $client = $this->serve(self::keyStatus(200, 'executed'), FakeServer::drop(), self::keyStatus(200, 'replayed'));

        self::book($client, 'a b');
        self::book($client, 'c/d');

        self::assertEquals(3, $this->server->requestCount());
    }

    public function testRoutesAreRememberedSeparately()
    {
        $client = $this->serve(self::keyStatus(200, 'executed'), FakeServer::drop(), FakeServer::status(200));

        self::book($client);
        self::failure(fn() => $client->client->post('/events/{key}/actions/release', ['path_params' => ['key' => 'event1']]));

        self::assertEquals(2, $this->server->requestCount());
    }

    public function testMethodsAreRememberedSeparately()
    {
        $client = $this->serve(self::keyStatus(200, 'executed'), FakeServer::drop(), FakeServer::status(200));

        self::book($client, 'event1', 'POST');
        self::failure(fn() => self::book($client, 'event1', 'DELETE'));

        self::assertEquals(2, $this->server->requestCount());
    }

    public static function ignoredStatuses(): array
    {
        return [[200], [400], [500]];
    }

    #[DataProvider('ignoredStatuses')]
    public function testTheLatestKnownStatusOfARouteWins(int $ignoredStatus)
    {
        $client = $this->serve(self::keyStatus(200, 'executed'), self::keyStatus($ignoredStatus, 'ignored'), FakeServer::drop(), FakeServer::status(200));

        self::book($client);
        try {
            self::book($client);
        } catch (SeatsioException) {
        }
        self::failure(fn() => self::book($client));

        self::assertEquals(3, $this->server->requestCount());
    }

    public static function statusesWithoutAKeyStatus(): array
    {
        return [[401], [429]];
    }

    #[DataProvider('statusesWithoutAKeyStatus')]
    public function testAResponseWithoutAStatusLeavesTheMemoryAlone(int $status)
    {
        $client = $this->serve(self::keyStatus(200, 'executed'), FakeServer::error($status, 'SOME_ERROR'), FakeServer::drop(), self::keyStatus(200, 'replayed'));

        self::book($client);
        self::failure(fn() => self::book(self::maxRetries($client, 0)));
        self::book(self::maxRetries($client, 1));

        self::assertEquals(4, $this->server->requestCount());
    }

    public function testConcretePathsOfTheSameTemplateShareTheMemory()
    {
        $client = $this->serve(self::keyStatus(200, 'executed'), FakeServer::drop(), self::keyStatus(200, 'replayed'));

        self::book($client, 'event1');
        self::book($client, 'event2');

        self::assertEquals(3, $this->server->requestCount());
        self::assertEquals('/events/event2/actions/book', $this->server->requests()[2]['path']);
    }

    public function testCopiesWithAKeyShareTheMemory()
    {
        $client = $this->serve(self::keyStatus(200, 'executed'), FakeServer::drop(), self::keyStatus(200, 'replayed'));

        self::book($client);
        self::book($client->withIdempotencyKey(self::KEY));

        self::assertEquals(3, $this->server->requestCount());
        self::assertEquals([self::KEY, self::KEY], array_slice($this->keys(), 1));
    }

    public function testOtherClientsDontShareTheMemory()
    {
        $this->serve(self::keyStatus(200, 'executed'), FakeServer::drop(), FakeServer::status(200));

        self::book($this->clientFor($this->server->url(), 'aSecretKey'));
        self::failure(fn() => self::book($this->clientFor($this->server->url(), 'anotherSecretKey')));

        self::assertEquals(2, $this->server->requestCount());
    }

    public function testARetrySendsTheSameRequest()
    {
        $this->serve(self::keyStatus(200, 'executed'), FakeServer::drop(), self::keyStatus(200, 'replayed'));
        $client = $this->clientFor($this->server->url(), 'secretKey', 'aWorkspaceKey');

        self::book($client, 'event1');
        $client->client->post(self::BOOK, ['path_params' => ['key' => 'event2'], 'query' => ['expand' => 'x'], 'body' => '{"objects": ["A-1"]}']);

        [, $lost, $retry] = $this->server->requests();
        self::assertEquals('POST', $lost['method']);
        self::assertEquals($lost['method'], $retry['method']);
        self::assertEquals('/events/event2/actions/book?expand=x', $lost['path']);
        self::assertEquals($lost['path'], $retry['path']);
        self::assertEquals('{"objects": ["A-1"]}', $lost['body']);
        self::assertEquals($lost['body'], $retry['body']);
        foreach (['idempotency-key', 'x-workspace-key', 'authorization'] as $header) {
            self::assertEquals(1, $this->server->headerCount(2, $header));
            self::assertEquals($lost['headers'][$header], $retry['headers'][$header]);
        }
    }

    public function testAnUnknownStatusLeavesTheMemoryAlone()
    {
        $client = $this->serve(self::keyStatus(200, 'executed'), self::keyStatus(200, 'something-new'), FakeServer::drop(), self::keyStatus(200, 'replayed'));

        self::book($client);
        self::book($client);
        self::book($client);

        self::assertEquals(4, $this->server->requestCount());
    }

    public function testAnUnknownStatusDoesNotMarkARouteAsHonoringKeys()
    {
        $client = $this->serve(self::keyStatus(200, 'something-new'), FakeServer::drop(), FakeServer::status(200));

        self::book($client);
        self::failure(fn() => self::book($client));

        self::assertEquals(2, $this->server->requestCount());
    }

    public function testTheStatusIsCaseInsensitive()
    {
        $client = $this->serve(self::keyStatus(200, 'Executed'), FakeServer::drop(), self::keyStatus(200, 'replayed'));

        self::book($client);
        self::book($client);

        self::assertEquals(3, $this->server->requestCount());
    }

    public function testInUseMarksARouteAsHonoringKeys()
    {
        $client = $this->serve(self::keyStatusError(409, 'IDEMPOTENCY_KEY_IN_USE', 'in-use'), FakeServer::drop(), self::keyStatus(200, 'replayed'));

        self::failure(fn() => self::book(self::maxRetries($client, 0)));
        self::book(self::maxRetries($client, 1));

        self::assertEquals(3, $this->server->requestCount());
    }

    public function testReusedMarksARouteAsHonoringKeys()
    {
        $client = $this->serve(self::keyStatusError(409, 'IDEMPOTENCY_KEY_REUSED', 'reused'), FakeServer::drop(), self::keyStatus(200, 'replayed'));

        self::failure(fn() => self::book($client));
        self::book($client);

        self::assertEquals(3, $this->server->requestCount());
    }

    public function testReplayedMarksARouteAsHonoringKeys()
    {
        $client = $this->serve(self::keyStatus(200, 'replayed'), FakeServer::drop(), self::keyStatus(200, 'replayed'));

        self::book($client);
        self::book($client);

        self::assertEquals(3, $this->server->requestCount());
    }

    public function testAReplayedErrorIsReportedAsReplayed()
    {
        $client = $this->serve(self::keyStatusError(400, 'EVENT_NOT_FOUND', 'replayed'));

        $e = self::failure(fn() => self::book($client));

        self::assertTrue($e->replayed);
        self::assertTrue($e->idempotencyKeyHonored);
        self::assertFalse($e->outcomeIsUnknown());
    }

    public function testAReplayed5xxIsNotSafeToRetryWithTheSameKey()
    {
        $client = $this->serve(self::keyStatusError(500, 'SERVER_ERROR', 'replayed'));

        $e = self::failure(fn() => self::book($client));

        self::assertTrue($e->outcomeIsUnknown());
        self::assertFalse($e->isSafeToRetryWithSameKey());
    }

    public function testAnErrorWithoutAStatusSaysNothingAboutTheKey()
    {
        $client = $this->serve(FakeServer::error(400, 'EVENT_NOT_FOUND'));

        $e = self::failure(fn() => self::book($client));

        self::assertNull($e->idempotencyKeyHonored);
        self::assertFalse($e->replayed);
    }

    public function testLostResponsesCountAgainstMaxRetries()
    {
        $client = $this->serve(self::keyStatus(200, 'executed'), FakeServer::drop());

        self::book($client);
        $e = self::failure(fn() => self::book(self::maxRetries($client, 2)));

        self::assertEquals(1 + 3, $this->server->requestCount());
        self::assertEquals(2, $this->clock->sleeps);
        self::assertTrue($e->outcomeIsUnknown());
        self::assertTrue($e->isSafeToRetryWithSameKey());
    }

    public function testKeyInUseErrorIsThrownWhenRetriesRunOut()
    {
        $client = $this->serve(FakeServer::error(409, 'IDEMPOTENCY_KEY_IN_USE'));

        $e = self::failure(fn() => self::book(self::maxRetries($client, 2)));

        self::assertEquals(3, $this->server->requestCount());
        self::assertEquals('IDEMPOTENCY_KEY_IN_USE', $e->errors[0]->code);
        self::assertTrue($e->outcomeIsUnknown());
    }

    public function testOutcomeStaysUnknownWhenARetryAfterKeyInUseIsRateLimited()
    {
        $client = $this->serve(self::keyStatusError(409, 'IDEMPOTENCY_KEY_IN_USE', 'in-use'), FakeServer::error(429, 'RATE_LIMIT_EXCEEDED'));

        $e = self::failure(fn() => self::book(self::maxRetries($client, 1)));

        self::assertInstanceOf(RateLimitExceededException::class, $e);
        self::assertEquals(2, $this->server->requestCount());
        self::assertTrue($e->outcomeIsUnknown());
        self::assertTrue($e->isSafeToRetryWithSameKey());
    }

    public function testOutcomeStaysUnknownWhenARetryAfterALostResponseIsRateLimited()
    {
        $client = $this->serve(self::keyStatus(200, 'executed'), FakeServer::drop(), FakeServer::error(429, 'RATE_LIMIT_EXCEEDED'));

        self::book($client);
        $e = self::failure(fn() => self::book(self::maxRetries($client, 1)));

        self::assertInstanceOf(RateLimitExceededException::class, $e);
        self::assertEquals(3, $this->server->requestCount());
        self::assertTrue($e->outcomeIsUnknown());
        self::assertTrue($e->isSafeToRetryWithSameKey());
    }

    public function testARequestThatWasNeverSentIsRetriedWhateverTheTimeIfNoAttemptWasSent()
    {
        [, $client] = $this->clientOnFreePort();
        $this->clock->onSleep = fn() => $this->clock->now += 6 * 60 * 1000;

        $e = self::failure(fn() => self::book(self::maxRetries($client, 3)));

        self::assertEquals(3, $this->clock->sleeps);
        self::assertFalse($e->outcomeIsUnknown());
        self::assertTrue($e->isSafeToRetryWithSameKey());
    }

    public function testARequestThatWasNeverSentIsNotRetriedOnceTheKeyMayHaveExpired()
    {
        $client = $this->serve(FakeServer::error(429, 'RATE_LIMIT_EXCEEDED'));
        $this->clock->onSleep = function () {
            $this->server->close();
            $this->clock->now += 6 * 60 * 1000;
        };

        $e = self::failure(fn() => self::book($client));

        self::assertEquals(1, $this->server->requestCount());
        self::assertEquals(1, $this->clock->sleeps);
        self::assertNotNull($e->getPrevious());
        self::assertFalse($e->outcomeIsUnknown());
    }

    public function testAllKindsOfFailureShareOneRetryCount()
    {
        [$port, $client] = $this->clientOnFreePort();
        $this->clock->onSleep = function () use ($port) {
            if ($this->server === null) {
                $this->startServerOn($port,
                    FakeServer::error(429, 'RATE_LIMIT_EXCEEDED'),
                    self::keyStatusError(409, 'IDEMPOTENCY_KEY_IN_USE', 'in-use'),
                    FakeServer::drop(),
                    FakeServer::status(200));
            }
        };

        $e = self::failure(fn() => self::book(self::maxRetries($client, 3)));

        self::assertEquals(3, $this->server->requestCount());
        self::assertEquals(3, $this->clock->sleeps);
        self::assertTrue($e->outcomeIsUnknown());
        self::assertTrue($e->isSafeToRetryWithSameKey());
    }

    public function testMaxRetriesZeroGivesOneAttemptForEachKindOfFailure()
    {
        $client = self::maxRetries($this->serve(
            FakeServer::error(429, 'RATE_LIMIT_EXCEEDED'),
            self::keyStatusError(409, 'IDEMPOTENCY_KEY_IN_USE', 'in-use'),
            FakeServer::drop(),
            FakeServer::status(200)
        ), 0);

        self::failure(fn() => self::book($client));
        self::failure(fn() => self::book($client));
        self::failure(fn() => self::book($client));
        [, $neverSent] = $this->clientOnFreePort();
        self::failure(fn() => self::book(self::maxRetries($neverSent, 0)));

        self::assertEquals(3, $this->server->requestCount());
        self::assertEquals(0, $this->clock->sleeps);
    }

    public function testLostResponsesAreNotRetriedOnceTheKeyMayHaveExpired()
    {
        $client = $this->serve(self::keyStatus(200, 'executed'), FakeServer::drop());
        $this->clock->onSleep = fn() => $this->clock->now += 2 * 60 * 1000;

        self::book($client);
        $e = self::failure(fn() => self::book($client));

        self::assertEquals(1 + 4, $this->server->requestCount());
        self::assertTrue($e->outcomeIsUnknown());
        self::assertFalse($e->isSafeToRetryWithSameKey());
    }

    public function testRetriesOn429StopOnceTheKeyMayHaveExpired()
    {
        $client = $this->serve(FakeServer::error(429, 'RATE_LIMIT_EXCEEDED'));
        $this->clock->onSleep = fn() => $this->clock->now += 6 * 60 * 1000;

        $e = self::failure(fn() => self::book($client));

        self::assertInstanceOf(RateLimitExceededException::class, $e);
        self::assertEquals(2, $this->server->requestCount());
    }

    public function testWritesAreRetriedOn429WithTheSameKey()
    {
        $client = $this->serve(FakeServer::error(429, 'RATE_LIMIT_EXCEEDED'), FakeServer::status(204));

        $response = self::book($client);

        self::assertEquals(204, $response->getStatusCode());
        self::assertCount(2, $this->keys());
        self::assertEquals($this->keys()[0], $this->keys()[1]);
    }

    public function testWritesAreRetriedOnKeyInUseWithTheSameKey()
    {
        $client = $this->serve(FakeServer::error(409, 'IDEMPOTENCY_KEY_IN_USE'), FakeServer::status(204));

        $response = self::book($client);

        self::assertEquals(204, $response->getStatusCode());
        self::assertCount(2, $this->keys());
        self::assertEquals($this->keys()[0], $this->keys()[1]);
    }

    public function testCallerKeySurvivesARetry()
    {
        $client = $this->serve(FakeServer::error(409, 'IDEMPOTENCY_KEY_IN_USE'), FakeServer::status(204));

        self::book($client->withIdempotencyKey(self::KEY));

        self::assertEquals([self::KEY, self::KEY], $this->keys());
    }

    public static function otherConflicts(): array
    {
        return [
            ['{"errors": [{"code": "SOMETHING_ELSE", "message": "m"}]}'],
            ['{"errors": []}'],
            ['{}'],
            ['malformed'],
            [''],
        ];
    }

    #[DataProvider('otherConflicts')]
    public function testOtherConflictsAreNotRetried(string $body)
    {
        $client = $this->serve(FakeServer::json(409, $body), FakeServer::status(204));

        $e = self::failure(fn() => self::book($client));

        self::assertEquals(1, $this->server->requestCount());
        self::assertFalse($e->outcomeIsUnknown());
    }

    public function testReusedIsNotRetried()
    {
        $client = $this->serve(FakeServer::error(409, 'IDEMPOTENCY_KEY_REUSED'), FakeServer::status(204));

        $e = self::failure(fn() => self::book($client));

        self::assertEquals(1, $this->server->requestCount());
        self::assertEquals('IDEMPOTENCY_KEY_REUSED', $e->errors[0]->code);
        self::assertFalse($e->outcomeIsUnknown());
    }

    public static function serverErrors(): array
    {
        return [[500], [502], [503], [504]];
    }

    #[DataProvider('serverErrors')]
    public function testOutcomeIsUnknownAfterA5xx(int $status)
    {
        $client = $this->serve(FakeServer::error($status, 'SERVER_ERROR'));

        $e = self::failure(fn() => self::book($client));

        self::assertTrue($e->outcomeIsUnknown());
        self::assertFalse($e->isSafeToRetryWithSameKey());
    }

    public function testA5xxIsSafeToRetryWithTheSameKeyWhenTheKeyWasHonored()
    {
        $client = $this->serve(self::keyStatusError(500, 'SERVER_ERROR', 'executed'));

        $e = self::failure(fn() => self::book($client));

        self::assertTrue($e->isSafeToRetryWithSameKey());
    }

    public function testA5xxWithoutAStatusIsSafeToRetryWithTheSameKeyOnARouteThatHonorsKeys()
    {
        $client = $this->serve(self::keyStatus(200, 'executed'), FakeServer::error(500, 'SERVER_ERROR'));

        self::book($client);
        $e = self::failure(fn() => self::book($client));

        self::assertTrue($e->isSafeToRetryWithSameKey());
    }

    public function testA5xxIsNotSafeToRetryWithTheSameKeyWhenTheKeyWasIgnored()
    {
        $client = $this->serve(self::keyStatusError(500, 'SERVER_ERROR', 'ignored'));

        $e = self::failure(fn() => self::book($client));

        self::assertTrue($e->outcomeIsUnknown());
        self::assertFalse($e->idempotencyKeyHonored);
        self::assertFalse($e->isSafeToRetryWithSameKey());
    }

    public function testOutcomeIsUnknownWhenTheKeyIsStillInUseAfterAllRetries()
    {
        $client = $this->serve(self::keyStatusError(409, 'IDEMPOTENCY_KEY_IN_USE', 'in-use'));

        $e = self::failure(fn() => self::book(self::maxRetries($client, 0)));

        self::assertEquals(1, $this->server->requestCount());
        self::assertTrue($e->outcomeIsUnknown());
        self::assertTrue($e->isSafeToRetryWithSameKey());
    }

    public static function clientErrors(): array
    {
        return [[400], [404], [409], [422]];
    }

    #[DataProvider('clientErrors')]
    public function testOutcomeIsKnownAfterA4xx(int $status)
    {
        $client = $this->serve(FakeServer::error($status, 'CLIENT_ERROR'));

        $e = self::failure(fn() => self::book($client));

        self::assertFalse($e->outcomeIsUnknown());
        self::assertFalse($e->isSafeToRetryWithSameKey());
    }

    public function testGetsAreNotRetriedOnTopOfTheHttpClientAfterALostResponse()
    {
        $client = $this->serve(FakeServer::drop(), FakeServer::status(200));

        $e = self::failure(fn() => $client->client->get('/events'));

        self::assertEquals(1, $this->server->requestCount());
        self::assertNull($e->idempotencyKey);
        self::assertFalse($e->isSafeToRetryWithSameKey());
    }

    public function testALostResponseToAGetHasAnUnknownOutcome()
    {
        $client = $this->serve(FakeServer::drop());

        $e = self::failure(fn() => $client->client->get('/events'));

        self::assertTrue($e->outcomeIsUnknown());
    }

    public function testAGetThatWasNeverSentHasAKnownOutcome()
    {
        [, $client] = $this->clientOnFreePort();

        $e = self::failure(fn() => $client->client->get('/events'));

        self::assertFalse($e->outcomeIsUnknown());
        self::assertFalse($e->isSafeToRetryWithSameKey());
    }

    public function testGetsAreRetriedOn429UpToMaxRetries()
    {
        $client = self::maxRetries($this->serve(FakeServer::error(429, 'RATE_LIMIT_EXCEEDED')), 2);

        self::failure(fn() => $client->client->get('/events'));

        self::assertEquals(3, $this->server->requestCount());
        self::assertEquals([null, null, null], $this->keys());
    }

    public function testATruncatedResponseIsALostResponse()
    {
        $client = $this->serve(self::keyStatus(200, 'executed'), FakeServer::truncated(), self::keyStatus(200, 'replayed'));

        self::book($client);
        self::book($client);

        self::assertEquals(3, $this->server->requestCount());
        self::assertEquals($this->keys()[1], $this->keys()[2]);
    }

    public function testATimeoutAfterConnectingIsALostResponse()
    {
        $client = $this->serve(FakeServer::hang(), FakeServer::status(200));

        $e = self::failure(fn() => self::book($client, 'event1', 'POST', ['timeout' => 0.3]));

        self::assertEquals(1, $this->server->requestCount());
        self::assertEquals(0, $this->clock->sleeps);
        self::assertTrue($e->outcomeIsUnknown());
        self::assertInstanceOf(ConnectException::class, $e->getPrevious());
        self::assertEquals(28, $e->getPrevious()->getHandlerContext()['errno']);
    }

    public function testRequestConstructionErrorsAreNotRetried()
    {
        $client = $this->serve(FakeServer::status(200));

        $e = self::failure(fn() => $client->client->post('unknownProtocol://x', ['body' => '{}']));

        self::assertEquals(0, $this->clock->sleeps);
        self::assertFalse($e->outcomeIsUnknown());
        self::assertTrue($e->isSafeToRetryWithSameKey());
    }

    public function testHeadersAreSentOncePerAttempt()
    {
        $client = $this->serve(FakeServer::error(429, 'RATE_LIMIT_EXCEEDED'), FakeServer::status(200));

        self::book($client);

        self::assertEquals(2, $this->server->requestCount());
        for ($i = 0; $i < 2; $i++) {
            self::assertEquals(1, $this->server->headerCount($i, 'X-Client-Lib'));
            self::assertEquals(1, $this->server->headerCount($i, 'Authorization'));
            self::assertEquals(1, $this->server->headerCount($i, 'Idempotency-Key'));
        }
    }

    public function testMultipartRequestsGetNoKey()
    {
        $client = $this->serve(FakeServer::status(200));

        $client->client->post('/upload', ['multipart' => [['name' => 'file', 'contents' => 'x']]]);

        self::assertEquals([null], $this->keys());
    }

    public function testMaxRetriesIsSharedWithClientsThatHaveAKey()
    {
        $client = $this->serve(FakeServer::error(429, 'RATE_LIMIT_EXCEEDED'));
        $withKey = $client->withIdempotencyKey(self::KEY);
        self::maxRetries($client, 1);

        self::failure(fn() => self::book($withKey));

        self::assertEquals(2, $this->server->requestCount());
    }

    public function testSetDefaultWorkspaceIsRetriedAfterALostResponse()
    {
        $client = $this->serve(
            FakeServer::keyStatus(FakeServer::status(204), 'executed'),
            FakeServer::drop(),
            FakeServer::keyStatus(FakeServer::status(204), 'replayed')
        );

        $client->workspaces->setDefault('workspace1');
        $client->workspaces->setDefault('workspace2');

        self::assertCount(3, $this->keys());
        self::assertEquals($this->keys()[1], $this->keys()[2]);
    }

    public function testAnOperationWithUnknownOutcomeCanBeRetriedWithTheKeyOfTheException()
    {
        $client = self::maxRetries($this->serve(self::keyStatus(200, 'executed'), FakeServer::drop(), self::keyStatus(200, 'replayed')), 0);

        self::book($client);
        $e = self::failure(fn() => self::book($client));
        self::assertTrue($e->isSafeToRetryWithSameKey());
        self::book($client->withIdempotencyKey($e->idempotencyKey));

        self::assertEquals($this->keys()[1], $this->keys()[2]);
    }
}
