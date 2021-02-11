<?php declare(strict_types=1);

namespace HJerichen\ClassInstantiator\Test\Helpers;

/**
 * @author Heiko Jerichen <heiko@jerichen.de>
 */
class ClassWithDependencyOfIntegerClass
{
    private int $integer;
    private ClassWithIntegerParameter $object;

    /** @noinspection UnusedConstructorDependenciesInspection */
    public function __construct(int $integer, ClassWithIntegerParameter $object)
    {
        $this->integer = $integer;
        $this->object = $object;
    }
}