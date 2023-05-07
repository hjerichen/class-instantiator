<?php
declare(strict_types=1);

namespace HJerichen\ClassInstantiator\FromReflection;

use HJerichen\ClassInstantiator\ClassInstantiator;
use HJerichen\ClassInstantiator\MethodInvoker;
use HJerichen\Collections\Reflection\ReflectionMethodCollection;
use HJerichen\Collections\Reflection\ReflectionPropertyCollection;
use ReflectionClass;
use ReflectionMethod;
use ReflectionNamedType;
use ReflectionProperty;

/**
 * @author Heiko Jerichen <heiko@jerichen.de>
 */
class ReflectionClassInstantiatorWithExtension implements ReflectionClassInstantiator
{
    private ReflectionClass $class;
    /** @var array<string,mixed> */
    private array $predefinedArguments;

    public function __construct(
        private readonly ReflectionClassInstantiator $reflectionClassInstantiator,
        private readonly ClassInstantiator $classInstantiator
    ) {
    }

    /**
     * @template T of object
     * @param ReflectionClass<T> $reflectionClass
     * @param array<string,mixed> $predefinedArguments
     * @return T|null
     * @noinspection PhpDocSignatureInspection
     * @psalm-suppress InvalidReturnStatement
     * @psalm-suppress InvalidReturnType
     */
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

    /** @psalm-suppress MixedAssignment */
    private function instantiateClassWithExtensionProperty(): ?object
    {
        $property = $this->getPropertyThatContainsWantedClass();
        $value = $property?->getValue($this->classInstantiator);
        return is_object($value) ? $value : null;
    }

    private function getPropertyThatContainsWantedClass(): ?ReflectionProperty
    {
        $properties = $this->getPropertiesOfClassInstantiator();
        foreach ($properties as $property) {
            $className = $this->class->getName();
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

    /** @psalm-suppress MixedAssignment */
    private function instantiateClassWithExtensionMethod(): ?object
    {
        $method = $this->getMethodThatReturnsWantedClass();
        if ($method === null) return null;

        $methodInvoker = new MethodInvoker($this->classInstantiator);
        $methodCallable = [$this->classInstantiator, $method->getName()];
        $result = $methodInvoker->invokeMethod($methodCallable, $this->predefinedArguments);
        return is_object($result) ? $result : null;
    }

    private function getMethodThatReturnsWantedClass(): ?ReflectionMethod
    {
        $methods = $this->getMethodsFromClassInstantiator();
        foreach ($methods as $method) {
            $returnType = $method->getReturnType();
            if (!($returnType instanceof ReflectionNamedType)) continue;

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