<?php

namespace HJerichen\ClassInstantiator;

use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;

/**
 * @author Heiko Jerichen <heiko@jerichen.de>
 */
class ClassInstantiatorFromReflection
{
    /**
     * ClassInstantiator
     */
    private $classInstantiator;
    /**
     * @var ReflectionClass
     */
    private $wantedClass;
    /**
     * @var array
     */
    private $predefinedArguments = [];

    public function __construct(ClassInstantiator $classInstantiator, ReflectionClass $reflectionClass)
    {
        $this->classInstantiator = $classInstantiator;
        $this->wantedClass = $reflectionClass;
    }

    public function instantiateClass($predefinedArguments): object
    {
        $this->predefinedArguments = $predefinedArguments;

        return $this->instantiateClassWithExtension() ?? $this->instantiateClassFromReflectionDynamically();
    }

    private function instantiateClassWithExtension(): ?object
    {
        return $this->instantiateClassWithExtensionProperty() ?? $this->instantiateClassWithExtensionMethod();
    }

    private function instantiateClassFromReflectionDynamically(): object
    {
        $argumentBuilder = new ArgumentsForConstructorBuilder($this->classInstantiator, $this->wantedClass);
        $argumentBuilder->setPredefinedArguments($this->predefinedArguments);
        $arguments = $argumentBuilder->buildArguments();
        return $this->wantedClass->newInstanceArgs($arguments);
    }

    private function instantiateClassWithExtensionMethod(): ?object
    {
        $method = $this->getMethodThatReturnsWantedClass();
        if ($method === null) return null;

        $methodInvoker = new MethodInvoker($this->classInstantiator);
        $methodCallable = [$this->classInstantiator, $method->getName()];
        return $methodInvoker->invokeMethod($methodCallable, $this->predefinedArguments);
    }

    private function getMethodThatReturnsWantedClass(): ?ReflectionMethod
    {
        $methods = $this->getMethodsFromClassInstantiator();
        foreach ($methods as $method) {
            $returnType = (string)$method->getReturnType();
            if ($returnType === $this->wantedClass->getName()) {
                return $method;
            }
        }
        return null;
    }

    /**
     * @return array<ReflectionMethod>
     */
    private function getMethodsFromClassInstantiator(): array
    {
        $ownReflection = new ReflectionClass($this->classInstantiator);
        return $ownReflection->getMethods();
    }

    private function instantiateClassWithExtensionProperty(): ?object
    {
        $property = $this->getPropertyThatContainsWantedClass();
        return $property ? $property->getValue($this->classInstantiator) : null;
    }

    private function getPropertyThatContainsWantedClass(): ?ReflectionProperty
    {
        $properties = $this->getPropertiesOfClassInstantiator();
        foreach ($properties as $property) {
            $className = $this->wantedClass->getName();
            $property->setAccessible(true);
            if ($property->getValue($this->classInstantiator) instanceof $className) {
                return $property;
            }
        }
        return null;
    }

    /**
     * @return array<ReflectionProperty>
     */
    private function getPropertiesOfClassInstantiator(): array
    {
        $ownReflection = new ReflectionClass($this->classInstantiator);
        return $ownReflection->getProperties();
    }

}