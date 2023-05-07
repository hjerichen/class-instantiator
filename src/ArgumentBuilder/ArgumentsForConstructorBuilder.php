<?php declare(strict_types=1);

namespace HJerichen\ClassInstantiator\ArgumentBuilder;

use HJerichen\ClassInstantiator\ClassInstantiator;
use HJerichen\Collections\Reflection\ReflectionParameterCollection;
use ReflectionClass;

/**
 * @author Heiko Jerichen <heiko@jerichen.de>
 */
class ArgumentsForConstructorBuilder
{
    private readonly ArgumentForParameterBuilder $argumentForParameterBuilder;

    public function __construct(
        ClassInstantiator $classInstantiator,
        private readonly ReflectionClass $reflectionClass
    ) {
        $this->argumentForParameterBuilder = new ArgumentForParameterBuilder($classInstantiator);
    }

    /** @param array<string,mixed> $predefinedArguments */
    public function setPredefinedArguments(array $predefinedArguments): void
    {
        $this->argumentForParameterBuilder->setPredefinedArguments($predefinedArguments);
    }

    public function buildArguments(): array
    {
        $parameters = $this->getParametersOfClassConstructor();
        return $this->buildArgumentsForParameters($parameters);
    }

    private function getParametersOfClassConstructor(): ReflectionParameterCollection
    {
        $constructor = $this->reflectionClass->getConstructor();
        $parameters = $constructor === null ? [] : $constructor->getParameters();
        return new ReflectionParameterCollection($parameters);
    }

    private function buildArgumentsForParameters(ReflectionParameterCollection $parameters): array
    {
        return $this->argumentForParameterBuilder->buildArgumentsForParameters($parameters);
    }
}