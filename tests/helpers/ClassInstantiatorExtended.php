<?php
/** @noinspection PhpUnused */
/** @noinspection UnusedConstructorDependenciesInspection */
declare(strict_types=1);

namespace HJerichen\ClassInstantiator\TestHelpers;

use HJerichen\ClassInstantiator\ClassInstantiator;

/**
 * @author Heiko Jerichen <heiko@jerichen.de>
 */
class ClassInstantiatorExtended extends ClassInstantiator
{
    /**
     * @var Environment
     */
    private $environment;

    public function __construct()
    {
        $this->environment = new Environment(4);
    }

    public function createIntegerObject(): ClassWithIntegerParameter
    {
        return new ClassWithIntegerParameter(5);
    }

    public function createIntegerObjectWithParameter(int $value): ClassWithTwoIntegerParameters
    {
        return new ClassWithTwoIntegerParameters(5, $value);
    }

    public function createObjectWithDependency(SimpleClass $simpleClass): ClassWithSimpleDependency
    {
        return new ClassWithSimpleDependency($simpleClass);
    }

    public function createSome(): SomeInterface
    {
        return new SomeInterfaceImplementation();
    }
}