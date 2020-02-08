<?php

namespace HJerichen\ClassInstantiator;

use ReflectionClass;
use ReflectionParameter;

/**
 * @author Heiko Jerichen <heiko@jerichen.de>
 */
class ArgumentsForConstructorBuilder
{
    /**
     * @var ArgumentForParameterBuilder
     */
    private $argumentForParameterBuilder;
    /**
     * @var ReflectionClass
     */
    private $reflectionClass;

    public function __construct(ClassInstantiator $classInstantiator, ReflectionClass $reflectionClass)
    {
        $this->argumentForParameterBuilder = new ArgumentForParameterBuilder($classInstantiator);
        $this->reflectionClass = $reflectionClass;
    }

    public function setPredefinedArguments(array $predefinedArguments): void
    {
        $this->argumentForParameterBuilder->setPredefinedArguments($predefinedArguments);
    }

    public function buildArguments(): array
    {
        $parameters = $this->getParametersOfClassConstructor();
        return $this->buildArgumentsForParameters($parameters);
    }

    /**
     * @return array<ReflectionParameter>
     */
    private function getParametersOfClassConstructor(): array
    {
        $constructor = $this->reflectionClass->getConstructor();
        if ($constructor === null) return [];

        return $constructor->getParameters();
    }

    private function buildArgumentsForParameters(array $parameters): array
    {
        return $this->argumentForParameterBuilder->buildArgumentsForParameters($parameters);
    }
}