<?php declare(strict_types=1);

namespace HJerichen\ClassInstantiator\FromReflection;

use HJerichen\ClassInstantiator\ArgumentBuilder\ArgumentsForConstructorBuilder;
use HJerichen\ClassInstantiator\ClassInstantiator;
use ReflectionClass;

/**
 * @author Heiko Jerichen <heiko@jerichen.de>
 */
readonly class ReflectionClassInstantiatorBase implements ReflectionClassInstantiator
{
    public function __construct(
        private ClassInstantiator $classInstantiator
    ) {
    }

    #[\Override]
    public function instantiateClass(ReflectionClass $reflectionClass, array $predefinedArguments): ?object
    {
        $argumentBuilder = new ArgumentsForConstructorBuilder($this->classInstantiator, $reflectionClass);
        $argumentBuilder->setPredefinedArguments($predefinedArguments);
        $arguments = $argumentBuilder->buildArguments();
        return $reflectionClass->newInstanceArgs($arguments);
    }
}