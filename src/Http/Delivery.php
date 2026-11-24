<?php

namespace Seatsio\Http;

use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\TransferException;

enum Delivery
{
    case NEVER_SENT;
    case RESPONSE_LOST;
    case NOT_RETRYABLE;

    private const COULDNT_RESOLVE_PROXY = 5;
    private const COULDNT_RESOLVE_HOST = 6;
    private const COULDNT_CONNECT = 7;
    private const OPERATION_TIMEDOUT = 28;
    private const SSL_CONNECT_ERROR = 35;
    private const PEER_FAILED_VERIFICATION = 60;
    private const UNSUPPORTED_PROTOCOL = 1;
    private const URL_MALFORMAT = 3;

    public static function of(\Throwable $failure): Delivery
    {
        if (!($failure instanceof TransferException)) {
            return self::NOT_RETRYABLE;
        }
        $context = $failure instanceof RequestException || $failure instanceof ConnectException ? $failure->getHandlerContext() : [];
        return match ($context['errno'] ?? null) {
            self::COULDNT_RESOLVE_PROXY, self::COULDNT_RESOLVE_HOST, self::COULDNT_CONNECT, self::SSL_CONNECT_ERROR, self::PEER_FAILED_VERIFICATION => self::NEVER_SENT,
            self::OPERATION_TIMEDOUT => self::timedOutBeforeConnecting($context) ? self::NEVER_SENT : self::RESPONSE_LOST,
            self::UNSUPPORTED_PROTOCOL, self::URL_MALFORMAT => self::NOT_RETRYABLE,
            default => self::RESPONSE_LOST,
        };
    }

    private static function timedOutBeforeConnecting(array $context): bool
    {
        return isset($context['connect_time'], $context['pretransfer_time'])
            && $context['connect_time'] == 0
            && $context['pretransfer_time'] == 0;
    }
}
