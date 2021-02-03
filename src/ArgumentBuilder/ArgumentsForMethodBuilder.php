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