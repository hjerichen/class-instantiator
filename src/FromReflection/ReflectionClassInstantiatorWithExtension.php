<?php declare(strict_types=1);

namespace HJerichen\ClassInstantiator\FromReflection;

use HJerichen\ClassInstantiator\ClassInstantiator;
use HJerichen\ClassInstantiator\MethodInvoker;
use HJerichen\Collections\Reflection\ReflectionMethodCollection;
use HJerichen\Collections\Reflection\ReflectionPropertyCollection;
use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;

/**
 * @author Heiko Jerichen <heiko@jerichen.de>
 */
class ReflectionClassInstantiatorWithExtension implements ReflectionClassInstantiator
{
    /** @var ClassInstantiator */
    private $classInstantiator;

    /** @var ReflectionClassInstantiator */
    private $reflectionClassInstantiator;

    /** @var array */
    private $predefinedArguments;

    /** @var ReflectionClass */
    private $class;

    public function __construct(ClassInstantiator $classInstantiatorSimple, ReflectionClassInstantiator $classInstantiator)
    {
        $this->classInstantiator = $classInstantiatorSimple;
        $this->reflectionClassInstantiator = $classInstantiator;
    }

    public function instantiateClass(ReflectionClass $reflectionClass, array $predefinedArguments): ?object
    {
        $this->class = $reflectionClass;
        $this->predefinedArguments = $predefinedArguments;

        return $this->instantiateClassWithExtension() ?? $this->instantiateClassWithImplementation();
    }

    private function instantiateClassWithExtension(): ?object
    {
        return $this->instantiateClassWithExtensionProperty() ?? $this->instantiateClassWithExtensionMethod();
    }

    private function instantiateClassWithImplementation(): ?object
    {
        return $this->reflectionClassInstantiator->instantiateClass($this->class, $this->predefinedArguments);
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
            $className = $this->class->getName();
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
            if ($returnType->getName() === $this->class->getName()) {
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
}