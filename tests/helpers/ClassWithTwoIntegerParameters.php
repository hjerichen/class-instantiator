<?php

namespace HJerichen\ClassInstantiator\TestHelpers;

/**
 * @author Heiko Jerichen <heiko@jerichen.de>
 */
class ClassWithTwoIntegerParameters
{
    /**
     * @var int
     */
    private $value1;
    /**
     * @var int
     */
    private $value2;

    /** @noinspection UnusedConstructorDependenciesInspection */
    public function __construct(int $value1, int $value2)
    {
        $this->value1 = $value1;
        $this->value2 = $value2;
    }
}