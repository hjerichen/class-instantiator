<?php declare(strict_types=1);

namespace HJerichen\ClassInstantiator\ArgumentBuilder;

use HJerichen\ClassInstantiator\ClassInstantiator;
use HJerichen\ClassInstantiator\Exception\InstantiateParameterException;
use HJerichen\Collections\Reflection\ReflectionParameterCollection;
use ReflectionParameter;

/**
 * @author Heiko Jerichen <heiko@jerichen.de>
 */
class ArgumentForParameterBuilder
{
    private array $predefinedArguments = [];

    public function __construct(
        private readonly ClassInstantiator $classInstantiator
    ) {
    }

    public function setPredefinedArguments(array $predefinedArguments): void
    {
        $this->predefinedArguments = $predefinedArguments;
    }

    public function buildArgumentsForParameters(ReflectionParameterCollection $parameters): array
    {
        $arguments = [];
        foreach ($parameters as $parameter) {
            $arguments[] = $this->buildArgumentForParameter($parameter);
        }
        return $arguments;
    }

    private function buildArgumentForParameter(ReflectionParameter $parameter)
    {
        return $this->getPredefinedArgumentForParameter($parameter) ?? $this->instantiateParameter($parameter);
    }

    /**
     * @param ReflectionParameter $parameter
     * @return mixed|null
     * @noinspection PhpMissingReturnTypeInspection
     */
    private function getPredefinedArgumentForParameter(ReflectionParameter $parameter)
    {
        if (!array_key_exists($parameter->getName(), $this->predefinedArguments)) {
            return null;
        }

        $argument = $this->predefinedArguments[$parameter->getName()];
        return $this->convertArgumentForParameter($argument, $parameter);
    }

    /**
     * @param $argument
     * @param ReflectionParameter $parameter
     * @return mixed
     * @noinspection PhpMissingReturnTypeInspection
     */
    private function convertArgumentForParameter($argument, ReflectionParameter $parameter)
    {
        if ($this->isArgumentAStringButIntegerIsNeeded($argument, $parameter)) {
            return (int)$argument;
        }
        return $argument;
    }

    private function isArgumentAStringButIntegerIsNeeded($argument, ReflectionParameter $parameter): bool
    {
        return is_string($argument) && is_numeric($argument) && $parameter->getType() && $parameter->getType()->getName();
    }

    private function instantiateParameter(ReflectionParameter $parameter): object
    {
        $parameterType = $parameter->getType();

        if (!$parameterType || $parameterType->isBuiltin()) {
            throw new InstantiateParameterException($parameter);
        }
        return $this->classInstantiator->instantiateClass($parameterType->getName(), $this->predefinedArguments);
    }
}