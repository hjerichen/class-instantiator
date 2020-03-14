<?php

namespace HJerichen\ClassInstantiator;

use HJerichen\ClassInstantiator\Exception\InstantiateParameterException;
use HJerichen\Collections\Reflection\ReflectionParameterCollection;
use ReflectionParameter;

/**
 * @author Heiko Jerichen <heiko@jerichen.de>
 */
class ArgumentForParameterBuilder
{
    /**
     * @var ClassInstantiator
     */
    private $classInstantiator;
    /**
     * @var array
     */
    private $predefinedArguments;

    public function __construct(ClassInstantiator $classInstantiator)
    {
        $this->classInstantiator = $classInstantiator;
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

    private function getPredefinedArgumentForParameter(ReflectionParameter $parameter)
    {
        if (!array_key_exists($parameter->getName(), $this->predefinedArguments)) {
            return null;
        }
        return $this->predefinedArguments[$parameter->getName()];
    }

    private function instantiateParameter(ReflectionParameter $parameter): object
    {
        $classOfParameter = $parameter->getClass();
        if ($classOfParameter === null) {
            throw new InstantiateParameterException($parameter);
        }
        return $this->classInstantiator->instantiateClass($classOfParameter->getName(), $this->predefinedArguments);
    }
}