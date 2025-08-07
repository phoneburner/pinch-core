<?php

declare(strict_types=1);

namespace PhoneBurner\Pinch\Exception;

class NotInstantiable extends \LogicException
{
    public function __construct(string $class)
    {
        parent::__construct(\sprintf('Class %s is not instantiable', $class));
    }
}
