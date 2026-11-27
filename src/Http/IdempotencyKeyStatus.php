<?php

namespace Seatsio\Http;

use Psr\Http\Message\ResponseInterface;

enum IdempotencyKeyStatus: string
{
    public const HEADER = 'Idempotency-Key-Status';

    case EXECUTED = 'executed';
    case REPLAYED = 'replayed';
    case IN_USE = 'in-use';
    case REUSED = 'reused';
    case IGNORED = 'ignored';

    public static function of(ResponseInterface $response): ?IdempotencyKeyStatus
    {
        return self::tryFrom(strtolower(trim($response->getHeaderLine(self::HEADER))));
    }

    public function keyHonored(): bool
    {
        return match ($this) {
            self::EXECUTED, self::REPLAYED, self::IN_USE, self::REUSED => true,
            self::IGNORED => false,
        };
    }
}
