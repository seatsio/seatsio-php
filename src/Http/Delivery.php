<?php

namespace Seatsio\Http;

use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ResponseException;
use GuzzleHttp\Exception\TransferException;

enum Delivery
{
    case NEVER_SENT;
    case RESPONSE_LOST;
    case NOT_RETRYABLE;

    private const CURL_ERROR_PREFIX = 'cURL error ';

    /**
     * Guzzle 8 tells transfer failures apart by exception type, not by a handler context:
     * a ConnectException (including ConnectTimeoutException) means no connection was made,
     * so the request never left the client. A plain RequestException that did not come from
     * cURL is Guzzle refusing the request before the transfer, such as an unsupported scheme.
     * Anything else may have reached the server.
     */
    public static function of(\Throwable $failure): Delivery
    {
        if (!($failure instanceof TransferException)) {
            return self::NOT_RETRYABLE;
        }
        if ($failure instanceof ConnectException) {
            return self::NEVER_SENT;
        }
        if (self::rejectedBeforeTransfer($failure)) {
            return self::NOT_RETRYABLE;
        }
        return self::RESPONSE_LOST;
    }

    private static function rejectedBeforeTransfer(TransferException $failure): bool
    {
        return $failure instanceof RequestException
            && !($failure instanceof ResponseException)
            && !str_starts_with($failure->getMessage(), self::CURL_ERROR_PREFIX);
    }
}
