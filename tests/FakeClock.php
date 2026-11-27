<?php

namespace Seatsio;

use Seatsio\Http\RetryClock;

class FakeClock extends RetryClock
{
    public int $now = 0;
    public int $sleeps = 0;
    public ?\Closure $onSleep = null;

    public function millis(): int
    {
        return $this->now;
    }

    public function sleep(int $millis): void
    {
        $this->sleeps++;
        $this->now += $millis;
        if ($this->onSleep !== null) {
            ($this->onSleep)();
        }
    }
}
