<?php declare(strict_types=1);

namespace HJerichen\ClassInstantiator\Test\Helpers;

/**
 * @author Heiko Jerichen <heiko@jerichen.de>
 */
class ClassWithIntegerParameter
{
    private int $value;

    /** @noinspection UnusedConstructorDependenciesInspection */
    public function __construct(int $value)
    {
        $this->value = $value;
    }
}