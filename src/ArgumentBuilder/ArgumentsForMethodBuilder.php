<?php declare(strict_types=1);

namespace HJerichen\ClassInstantiator\ArgumentBuilder;

use HJerichen\ClassInstantiator\ClassInstantiator;
use HJerichen\Collections\Reflection\ReflectionParameterCollection;
use ReflectionMethod;

/**
 * @author Heiko Jerichen <heiko@jerichen.de>
 */
class ArgumentsForMethodBuilder
{
    private readonly ArgumentForParameterBuilder $argumentForParameterBuilder;

    public function __construct(
        ClassInstantiator $classInstantiator,
        private readonly ReflectionMethod $reflectionMethod
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
        $parameters = $this->getParametersOfMethod();
        return $this->buildArgumentsForParameters($parameters);
    }

    private function getParametersOfMethod(): ReflectionParameterCollection
    {
        $parameters = $this->reflectionMethod->getParameters();
        return new ReflectionParameterCollection($parameters);
    }

    private function buildArgumentsForParameters(ReflectionParameterCollection $parameters): array
    {
        return $this->argumentForParameterBuilder->buildArgumentsForParameters($parameters);
    }
}