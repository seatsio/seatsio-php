<?php

namespace Seatsio\Http;

class RetryClock
{
    public function millis(): int
    {
        return intdiv(hrtime(true), 1_000_000);
    }

    public function sleep(int $millis): void
    {
        usleep($millis * 1000);
    }
}
