<?php

namespace HJerichen\ObjectFactory;

use ReflectionMethod;
use ReflectionParameter;

/**
 * @author Heiko Jerichen <heiko@jerichen.de>
 */
class ArgumentsForMethodBuilder
{
    /**
     * @var ArgumentForParameterBuilder
     */
    private $argumentForParameterBuilder;
    /**
     * @var ReflectionMethod
     */
    private $reflectionMethod;

    public function __construct(ClassInstantiator $classInstantiator, ReflectionMethod $reflectionMethod)
    {
        $this->argumentForParameterBuilder = new ArgumentForParameterBuilder($classInstantiator);
        $this->reflectionMethod = $reflectionMethod;
    }

    public function setPredefinedArguments(array $predefinedArguments): void
    {
        $this->argumentForParameterBuilder->setPredefinedArguments($predefinedArguments);
    }

    public function buildArguments(): array
    {
        $parameters = $this->getParametersOfMethod();
        return $this->buildArgumentsForParameters($parameters);
    }

    /**
     * @return array<ReflectionParameter>
     */
    private function getParametersOfMethod(): array
    {
        return $this->reflectionMethod->getParameters();
    }

    private function buildArgumentsForParameters(array $parameters): array
    {
        return $this->argumentForParameterBuilder->buildArgumentsForParameters($parameters);
    }
}