<?php

namespace Seatsio;

use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\TransferException;
use GuzzleHttp\Psr7\Request;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Seatsio\Http\Delivery;

class DeliveryTest extends TestCase
{
    public static function contexts(): array
    {
        return [
            'host not resolved' => [['errno' => 6], Delivery::NEVER_SENT],
            'connection refused' => [['errno' => 7], Delivery::NEVER_SENT],
            'TLS handshake failed' => [['errno' => 35], Delivery::NEVER_SENT],
            'timeout before connecting' => [['errno' => 28, 'connect_time' => 0.0, 'pretransfer_time' => 0.0], Delivery::NEVER_SENT],
            'timeout after connecting' => [['errno' => 28, 'connect_time' => 0.01, 'pretransfer_time' => 0.0], Delivery::RESPONSE_LOST],
            'timeout after sending' => [['errno' => 28, 'connect_time' => 0.01, 'pretransfer_time' => 0.02], Delivery::RESPONSE_LOST],
            'timeout without timings' => [['errno' => 28], Delivery::RESPONSE_LOST],
            'empty reply' => [['errno' => 52], Delivery::RESPONSE_LOST],
            'connection reset while receiving' => [['errno' => 56], Delivery::RESPONSE_LOST],
            'truncated body' => [['errno' => 18], Delivery::RESPONSE_LOST],
            'no errno' => [[], Delivery::RESPONSE_LOST],
            'unsupported protocol' => [['errno' => 1], Delivery::NOT_RETRYABLE],
        ];
    }

    #[DataProvider('contexts')]
    public function testClassifiesByCurlError(array $context, Delivery $expected)
    {
        $failure = new ConnectException('failed', new Request('POST', 'http://example.com'), null, $context);

        self::assertSame($expected, Delivery::of($failure));
    }

    public function testRequestExceptionsAreClassifiedToo()
    {
        $failure = new RequestException('failed', new Request('POST', 'http://example.com'), null, null, ['errno' => 7]);

        self::assertSame(Delivery::NEVER_SENT, Delivery::of($failure));
    }

    public function testTransferExceptionsWithoutAContextMayHaveLostTheResponse()
    {
        self::assertSame(Delivery::RESPONSE_LOST, Delivery::of(new TransferException('failed')));
    }

    public function testOtherFailuresAreNotRetried()
    {
        self::assertSame(Delivery::NOT_RETRYABLE, Delivery::of(new \InvalidArgumentException('bad')));
    }
}
