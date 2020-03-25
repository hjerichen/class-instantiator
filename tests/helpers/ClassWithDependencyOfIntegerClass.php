<?php declare(strict_types=1);

namespace HJerichen\ClassInstantiator\TestHelpers;

/**
 * @author Heiko Jerichen <heiko@jerichen.de>
 */
class ClassWithDependencyOfIntegerClass
{
    /**
     * @var int
     */
    private $integer;
    /**
     * @var ClassWithIntegerParameter
     */
    private $object;

    /** @noinspection UnusedConstructorDependenciesInspection */
    public function __construct(int $integer, ClassWithIntegerParameter $object)
    {
        $this->integer = $integer;
        $this->object = $object;
    }
}