<?php

declare(strict_types=1);

namespace PhoneBurner\Pinch\Attribute\Usage;

/**
 * Indicates that the class, method, or property should only be used in the
 * context of the specified classes.
 */
#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD | \Attribute::TARGET_PROPERTY)]
class RestrictTo
{
    /**
     * @var array<class-string>
     */
    public readonly array $classes;

    /**
     * @param class-string ...$classes
     */
    public function __construct(string ...$classes)
    {
        $this->classes = $classes ?: throw new \InvalidArgumentException(
            'At least one class string must be provided',
        );
    }
}
