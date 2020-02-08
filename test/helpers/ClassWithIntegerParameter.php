<?php

namespace HJerichen\ClassInstantiator\TestHelpers;

/**
 * @author Heiko Jerichen <heiko@jerichen.de>
 */
class ClassWithIntegerParameter
{
    /**
     * @var int
     */
    private $value;

    public function __construct(int $value)
    {
        $this->value = $value;
    }

    public function setValue(int $value): void
    {
        $this->value = $value;
    }
}