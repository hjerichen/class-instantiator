<?php

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

    /** @noinspection UnusedConstructorDependenciesInspection */
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

    /** @noinspection PhpUnusedPrivateMethodInspection */
    private function createSome(): SomeInterface
    {
        return new SomeInterfaceImplementation();
    }
}