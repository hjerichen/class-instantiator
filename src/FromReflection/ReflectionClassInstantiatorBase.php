<?php declare(strict_types=1);

namespace HJerichen\ClassInstantiator\FromReflection;

use HJerichen\ClassInstantiator\ArgumentBuilder\ArgumentsForConstructorBuilder;
use HJerichen\ClassInstantiator\ClassInstantiator;
use ReflectionClass;

/**
 * @author Heiko Jerichen <heiko@jerichen.de>
 */
class ReflectionClassInstantiatorBase implements ReflectionClassInstantiator
{
    private ClassInstantiator $classInstantiator;

    public function __construct(ClassInstantiator $classInstantiator)
    {
        $this->classInstantiator = $classInstantiator;
    }

    public function instantiateClass(ReflectionClass $reflectionClass, array $predefinedArguments): ?object
    {
        $argumentBuilder = new ArgumentsForConstructorBuilder($this->classInstantiator, $reflectionClass);
        $argumentBuilder->setPredefinedArguments($predefinedArguments);
        $arguments = $argumentBuilder->buildArguments();
        return $reflectionClass->newInstanceArgs($arguments);
    }
}