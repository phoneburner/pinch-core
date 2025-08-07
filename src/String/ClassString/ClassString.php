<?php

declare(strict_types=1);

namespace PhoneBurner\Pinch\String\ClassString;

/**
 * Immutable, self-validating wrapper object for class-string values.
 * Supports enums, classes, and interfaces, but not traits.
 *
 * @template T of object
 */
class ClassString implements \Stringable
{
    /**
     * @var class-string<T>
     */
    public string $value;

    public ClassStringType $type;

    /**
     * @phpstan-assert ClassString<T> $this
     */
    public function __construct(string $value)
    {
        $this->type = match (true) {
            \enum_exists($value) => ClassStringType::Enum,
            \class_exists($value) => ClassStringType::Object,
            \interface_exists($value) => ClassStringType::Interface,
            \trait_exists($value) => throw new \UnexpectedValueException("Traits are not supported"),
            default => throw new \UnexpectedValueException(\sprintf('Class %s does not exist', $value)),
        };

        /**
         * @var class-string<T> $value
         */
        $this->value = $value;
    }

    /**
     * @param class-string<T> $type
     * @return self<T>
     * @phpstan-assert class-string<T> $value
     */
    public static function match(string $value, string $type): self
    {
        if (\is_a($value, $type, true)) {
            /** @var ClassString<T> $class_string */
            $class_string = new self($value);
            return $class_string;
        }

        throw new \UnexpectedValueException(\sprintf("Class '%s' does not match type '%s'", $value, $type));
    }

    /**
     * @param self|object|class-string $class
     */
    public function is(object|string $class): bool
    {
        if (\is_object($class)) {
            $class = $class instanceof self ? $class->value : $class::class;
        }

        return \is_a($this->value, $class, true);
    }

    /**
     * @return \ReflectionClass<T>
     */
    public function reflect(): \ReflectionClass
    {
        return new \ReflectionClass($this->value);
    }

    /**
     * @return class-string<T>
     */
    public function __toString(): string
    {
        return $this->value;
    }

    /**
     * @return array{0: class-string<T>}
     */
    public function __serialize(): array
    {
        return [$this->value];
    }

    /**
     * @param array{0: class-string<T>} $data
     */
    public function __unserialize(array $data): void
    {
        $this->__construct($data[0]);
    }
}
