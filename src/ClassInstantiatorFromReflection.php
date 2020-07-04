<?php declare(strict_types=1);

namespace HJerichen\ClassInstantiator;

use HJerichen\Collections\Reflection\ReflectionMethodCollection;
use HJerichen\Collections\Reflection\ReflectionPropertyCollection;
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
            $returnType = $method->getReturnType();
            if ($returnType === null) continue;

            /** @noinspection PhpPossiblePolymorphicInvocationInspection */
            if ($returnType->getName() === $this->wantedClass->getName()) {
                return $method;
            }
        }
        return null;
    }

    private function getMethodsFromClassInstantiator(): ReflectionMethodCollection
    {
        $ownReflection = new ReflectionClass($this->classInstantiator);
        $methods = $ownReflection->getMethods();
        return new ReflectionMethodCollection($methods);
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

    private function getPropertiesOfClassInstantiator(): ReflectionPropertyCollection
    {
        $ownReflection = new ReflectionClass($this->classInstantiator);
        $properties = $ownReflection->getProperties();
        return new ReflectionPropertyCollection($properties);
    }

}