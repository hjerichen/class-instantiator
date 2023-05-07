<?php declare(strict_types=1);

namespace HJerichen\ClassInstantiator\FromReflection;

use HJerichen\ClassInstantiator\ObjectStore;
use ReflectionClass;

class ReflectionClassInstantiatorWithObjectStore implements ReflectionClassInstantiator
{
    public function __construct(
        private readonly ReflectionClassInstantiator $reflectionClassInstantiator,
        private readonly ObjectStore $objectStore
    ) {
    }

    /**
     * @template T of object
     * @param ReflectionClass<T> $reflectionClass
     * @param array<string,mixed> $predefinedArguments
     * @return T|null
     * @noinspection PhpDocSignatureInspection
     */
    public function instantiateClass(ReflectionClass $reflectionClass, array $predefinedArguments): ?object
    {
        if ($this->objectStore->hasObjectForClass($reflectionClass->getName())) {
            return $this->objectStore->retrieveObjectForClass($reflectionClass->getName());
        }
        return $this->reflectionClassInstantiator->instantiateClass($reflectionClass, $predefinedArguments);
    }
}