<?php

namespace Seatsio;

use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\ConnectTimeoutException;
use GuzzleHttp\Exception\HandlerClosedException;
use GuzzleHttp\Exception\NetworkException;
use GuzzleHttp\Exception\NetworkTimeoutException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ResponseTimeoutException;
use GuzzleHttp\Exception\ResponseTransferException;
use GuzzleHttp\Exception\TransferException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Seatsio\Http\Delivery;

class DeliveryTest extends TestCase
{
    public static function failures(): array
    {
        $request = new Request('POST', 'http://example.com');
        $response = new Response(200);
        return [
            'no connection' => [new ConnectException('cURL error 7: failed', $request), Delivery::NEVER_SENT],
            'timeout before connecting' => [new ConnectTimeoutException('cURL error 28: failed', $request), Delivery::NEVER_SENT],
            'timeout after connecting' => [new NetworkTimeoutException('cURL error 28: failed', $request), Delivery::RESPONSE_LOST],
            'timeout while receiving' => [new ResponseTimeoutException('cURL error 28: failed', $request, $response), Delivery::RESPONSE_LOST],
            'empty reply' => [new NetworkException('cURL error 52: failed', $request), Delivery::RESPONSE_LOST],
            'truncated body' => [new ResponseTransferException('cURL error 18: failed', $request, $response), Delivery::RESPONSE_LOST],
            'other cURL error' => [new RequestException('cURL error 26: failed', $request), Delivery::RESPONSE_LOST],
            'handler closed' => [new HandlerClosedException('closed', $request), Delivery::RESPONSE_LOST],
            'other transfer failure' => [new TransferException('failed', $request), Delivery::RESPONSE_LOST],
            'rejected before transfer' => [new RequestException("The scheme 'x' is not supported.", $request), Delivery::NOT_RETRYABLE],
            'not a transfer failure' => [new \InvalidArgumentException('bad'), Delivery::NOT_RETRYABLE],
        ];
    }

    #[DataProvider('failures')]
    public function testClassifiesByExceptionType(\Throwable $failure, Delivery $expected)
    {
        self::assertSame($expected, Delivery::of($failure));
    }
}
