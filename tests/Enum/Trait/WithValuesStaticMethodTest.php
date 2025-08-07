<?php

declare(strict_types=1);

namespace PhoneBurner\Pinch\Tests\Enum\Trait;

use PhoneBurner\Pinch\Tests\Fixtures\ArabicNumerals;
use PhoneBurner\Pinch\Tests\Fixtures\StoplightState;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class WithValuesStaticMethodTest extends TestCase
{
    #[Test]
    public function stringBackedValuesReturnExpectedArray(): void
    {
        self::assertSame([
                'Red' => 'red',
                'Yellow' => 'yellow',
                'Green' => 'green',
        ], StoplightState::values());
    }

    #[Test]
    public function integerBackedValuesReturnExpectedArray(): void
    {
        self::assertSame([
                'Zero' => 0,
                'One' => 1,
                'Two' => 2,
                'Three' => 3,
                'Four' => 4,
                'Five' => 5,
                'Six' => 6,
                'Seven' => 7,
                'Eight' => 8,
                'Nine' => 9,
        ], ArabicNumerals::values());
    }
}
