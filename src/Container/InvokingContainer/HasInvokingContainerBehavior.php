<?php

declare(strict_types=1);

namespace PhoneBurner\Pinch\Container\InvokingContainer;

use PhoneBurner\Pinch\Container\InvokingContainer;
use PhoneBurner\Pinch\Container\InvokingContainer\ReflectionMethodAutoResolver;
use PhoneBurner\Pinch\Container\ParameterOverride\OverrideCollection;

use function PhoneBurner\Pinch\Type\is_class;

/**
 * @phpstan-require-implements InvokingContainer
 */
trait HasInvokingContainerBehavior
{
    public function call(
        object|string $object,
        string $method = '__invoke',
        OverrideCollection|null $overrides = null,
    ): mixed {
        if ($object instanceof \Closure) {
            $reflection_function = new \ReflectionFunction($object);
            return $reflection_function->invokeArgs(\array_map(
                new ReflectionMethodAutoResolver($this, $overrides)(...),
                $reflection_function->getParameters(),
            ));
        }

        if (! is_class($object)) {
            throw new \UnexpectedValueException(\sprintf(
                'Expected $object to be object, class string, or callable, got "%s"',
                \gettype($object),
            ));
        }

        $object = \is_string($object) ? $this->get($object) : $object;
        if ($method === '__invoke' && ! \is_callable($object)) {
            throw new \UnexpectedValueException(\sprintf(
                'Object of class "%s" is not invokable',
                $object::class,
            ));
        }

        $reflection_method = new \ReflectionClass($object)->getMethod($method);
        return $reflection_method->invokeArgs($object, \array_map(
            new ReflectionMethodAutoResolver($this, $overrides)(...),
            $reflection_method->getParameters(),
        ));
    }
}
