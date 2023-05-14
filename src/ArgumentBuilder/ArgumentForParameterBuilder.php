<?php
/** @noinspection OneTimeUseVariablesInspection */
declare(strict_types=1);

namespace HJerichen\ClassInstantiator\ArgumentBuilder;

use HJerichen\ClassInstantiator\Attribute\InstantiatorOfAttributeLoader;
use HJerichen\ClassInstantiator\ClassInstantiator;
use HJerichen\ClassInstantiator\Exception\InstantiateParameterException;
use HJerichen\Collections\Reflection\ReflectionParameterCollection;
use ReflectionNamedType;
use ReflectionParameter;

/**
 * @author Heiko Jerichen <heiko@jerichen.de>
 */
class ArgumentForParameterBuilder
{
    /** @var array<string,mixed> */
    private array $predefinedArguments = [];

    public function __construct(
        private readonly ClassInstantiator $classInstantiator
    ) {
    }

    /** @param array<string,mixed> $predefinedArguments */
    public function setPredefinedArguments(array $predefinedArguments): void
    {
        $this->predefinedArguments = $predefinedArguments;
    }

    /** @return list<mixed> */
    public function buildArgumentsForParameters(ReflectionParameterCollection $parameters): array
    {
        /** @var list<mixed> $arguments */
        $arguments = [];
        foreach ($parameters as $parameter) {
            /** @psalm-suppress MixedAssignment Does not need to be determined. */
            $arguments[] = $this->buildArgumentForParameter($parameter);
        }
        return $arguments;
    }

    private function buildArgumentForParameter(ReflectionParameter $parameter): mixed
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

        /** @psalm-suppress MixedAssignment Does not need to be determined. */
        $argument = $this->predefinedArguments[$parameter->getName()];
        return $this->convertArgumentForParameter($argument, $parameter);
    }

    /**
     * @param mixed $argument
     * @param ReflectionParameter $parameter
     * @return mixed
     * @noinspection PhpMissingReturnTypeInspection
     */
    private function convertArgumentForParameter(mixed $argument, ReflectionParameter $parameter)
    {
        if ($this->isArgumentAStringButIntegerIsNeeded($argument, $parameter)) {
            return (int)$argument;
        }
        return $argument;
    }

    private function isArgumentAStringButIntegerIsNeeded(mixed $argument, ReflectionParameter $parameter): bool
    {
        return
            is_string($argument) &&
            is_numeric($argument) &&
            $parameter->getType() instanceof ReflectionNamedType &&
            $parameter->getType()->getName();
    }

    private function instantiateParameter(ReflectionParameter $parameter): object
    {
        $parameterType = $parameter->getType();

        if (!($parameterType instanceof ReflectionNamedType) || $parameterType->isBuiltin()) {
            throw new InstantiateParameterException($parameter);
        }

        $classInstantiator = $this->getInstantiatorForParameter($parameter);
        return $classInstantiator->instantiateClass($parameterType->getName(), $this->predefinedArguments);
    }

    private function getInstantiatorForParameter(ReflectionParameter $parameter): ClassInstantiator
    {
        $instantiatorLoader = new InstantiatorOfAttributeLoader($this->classInstantiator);
        return $instantiatorLoader->executeForAttributeList($parameter->getAttributes()) ?? $this->classInstantiator;
    }
}