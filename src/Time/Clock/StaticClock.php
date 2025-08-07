<?php

declare(strict_types=1);

namespace PhoneBurner\Pinch\Time\Clock;

use Carbon\CarbonImmutable;
use PhoneBurner\Pinch\Time\TimeUnit;

final readonly class StaticClock implements Clock
{
    private CarbonImmutable $now;

    public function __construct(\DateTimeInterface|string|null $now = new CarbonImmutable())
    {
        $this->now = match (true) {
            $now instanceof CarbonImmutable => $now,
            $now instanceof \DateTimeInterface => CarbonImmutable::instance($now),
            default => new CarbonImmutable($now),
        };
    }

    #[\Override]
    public function now(): CarbonImmutable
    {
        return $this->now;
    }

    public function timestamp(): int
    {
        return $this->now->getTimestamp();
    }

    public function microtime(): float
    {
        return (float)$this->now->format('U.u');
    }

    public function sleep(int $delay, TimeUnit $unit = TimeUnit::Microsecond): true
    {
        return true;
    }
}
