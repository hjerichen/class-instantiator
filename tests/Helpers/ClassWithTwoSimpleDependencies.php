<?php declare(strict_types=1);

namespace HJerichen\ClassInstantiator\Test\Helpers;

/**
 * @author Heiko Jerichen <heiko@jerichen.de>
 */
class ClassWithTwoSimpleDependencies
{
    private SimpleClass $simpleClass;
    private ClassWithSimpleDependency $classWithDependency;

    /** @noinspection UnusedConstructorDependenciesInspection */
    public function __construct(SimpleClass $simpleClass, ClassWithSimpleDependency $classWithDependency)
    {
        $this->simpleClass = $simpleClass;
        $this->classWithDependency = $classWithDependency;
    }
}