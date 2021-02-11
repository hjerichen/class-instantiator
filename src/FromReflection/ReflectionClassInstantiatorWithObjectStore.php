<?php declare(strict_types=1);

namespace HJerichen\ClassInstantiator\FromReflection;

use HJerichen\ClassInstantiator\ObjectStore;
use ReflectionClass;

class ReflectionClassInstantiatorWithObjectStore implements ReflectionClassInstantiator
{
    private ReflectionClassInstantiator $reflectionClassInstantiator;
    private ObjectStore $objectStore;

    public function __construct(ReflectionClassInstantiator $reflectionClassInstantiator, ObjectStore $objectStore)
    {
        $this->reflectionClassInstantiator = $reflectionClassInstantiator;
        $this->objectStore = $objectStore;
    }

    public function instantiateClass(ReflectionClass $reflectionClass, array $predefinedArguments): ?object
    {
        if ($this->objectStore->hasObjectForClass($reflectionClass->getName())) {
            return $this->objectStore->retrieveObjectForClass($reflectionClass->getName());
        }
        return $this->reflectionClassInstantiator->instantiateClass($reflectionClass, $predefinedArguments);
    }
}