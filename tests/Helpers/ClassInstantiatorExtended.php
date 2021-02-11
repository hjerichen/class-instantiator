<?php declare(strict_types=1);

namespace HJerichen\ClassInstantiator\Test\Helpers;

use HJerichen\ClassInstantiator\ClassInstantiator;

/**
 * @author Heiko Jerichen <heiko@jerichen.de>
 */
class ClassInstantiatorExtended extends ClassInstantiator
{
    private Environment $environment;

    /** @noinspection UnusedConstructorDependenciesInspection */
    public function __construct()
    {
        parent::__construct();
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

    public function createSome2(): SomeInterface2
    {
        return new SomeInterface2Implementation(2);
    }
}