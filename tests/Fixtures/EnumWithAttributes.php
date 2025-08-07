<?php

declare(strict_types=1);

namespace PhoneBurner\Pinch\Tests\Fixtures;

use PhoneBurner\Pinch\Tests\Fixtures\Attributes\MockRepeatableEnumAttribute;

enum EnumWithAttributes: string
{
    #[MockRepeatableEnumAttribute('Case A Value')]
    case CaseA = 'a';

    #[MockRepeatableEnumAttribute('Case B Value')]
    case CaseB = 'b';
}
