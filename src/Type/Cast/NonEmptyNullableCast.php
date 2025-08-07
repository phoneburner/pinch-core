<?php

declare(strict_types=1);

namespace PhoneBurner\Pinch\Type\Cast;

use PhoneBurner\Pinch\Trait\HasNonInstantiableBehavior;

use function PhoneBurner\Pinch\Type\get_debug_value;

final readonly class NonEmptyNullableCast
{
    use HasNonInstantiableBehavior;

    /**
     * @return positive-int|negative-int|null
     */
    public static function integer(mixed $value): int|null
    {
        return match (true) {
            \is_int($value), $value === null => $value,
            \is_scalar($value) => (int)$value,
            default => throw new \InvalidArgumentException(
                \sprintf('Expected scalar or null, got %s', get_debug_value($value)),
            ),
        } ?: null;
    }

    public static function float(mixed $value): float|null
    {
        return match (true) {
            \is_float($value), $value === null => $value,
            \is_scalar($value) => (float)$value,
            default => throw new \InvalidArgumentException(
                \sprintf('Expected scalar or null, got %s', get_debug_value($value)),
            ),
        } ?: null;
    }

    /**
     * @return non-empty-string|null
     */
    public static function string(mixed $value): string|null
    {
        return match (true) {
            \is_string($value), $value === null => $value,
            \is_scalar($value) => (string)$value,
            default => throw new \InvalidArgumentException(
                \sprintf('Expected scalar or null, got %s', get_debug_value($value)),
            ),
        } ?: null;
    }

    public static function boolean(mixed $value): true|null
    {
        return $value ? true : null;
    }

    /**
     * @param array<*>|null $value
     * @return non-empty-array<*>|null
     */
    public static function array(array|null $value): array|null
    {
        return $value ?: null;
    }
}
