<?php declare(strict_types=1);

namespace HJerichen\ClassInstantiator;

use HJerichen\ClassInstantiator\FromReflection\ReflectionClassInstantiator;
use HJerichen\ClassInstantiator\FromReflection\ReflectionClassInstantiatorBase;
use HJerichen\ClassInstantiator\Exception\UnknownClassException;
use HJerichen\ClassInstantiator\FromReflection\ReflectionClassInstantiatorWithAnnotation;
use HJerichen\ClassInstantiator\FromReflection\ReflectionClassInstantiatorWithExtension;
use HJerichen\ClassInstantiator\FromReflection\ReflectionClassInstantiatorWithObjectStore;
use ReflectionClass;
use ReflectionException;

/**
 * @author Heiko Jerichen <heiko@jerichen.de>
 */
class ClassInstantiator
{
    private ObjectStore $objectStore;

    public function __construct()
    {
        $this->objectStore = new ObjectStore();
    }

    public function instantiateClass(string $class, array $predefinedArguments = []): object
    {
        try {
            $reflectionClass = new ReflectionClass($class);
        } catch (ReflectionException $exception) {
            throw new UnknownClassException($class);
        }
        return $this->instantiateClassFromReflection($reflectionClass, $predefinedArguments);
    }

    public function instantiateClassFromReflection(ReflectionClass $class, $predefinedArguments = []): object
    {
        $reflectionClassInstantiator = $this->createReflectionClassInstantiator();
        return $reflectionClassInstantiator->instantiateClass($class, $predefinedArguments);
    }

    public function injectObject(object $object, ?string $class = null): void
    {
        $this->objectStore->storeObject($object, $class);
    }

    protected function createReflectionClassInstantiator(): ReflectionClassInstantiator
    {
        $classInstantiator = new ReflectionClassInstantiatorBase($this);
        $classInstantiator = new ReflectionClassInstantiatorWithAnnotation($classInstantiator, $this, $this->objectStore);
        $classInstantiator = new ReflectionClassInstantiatorWithExtension($classInstantiator, $this);
        $classInstantiator = new ReflectionClassInstantiatorWithObjectStore($classInstantiator, $this->objectStore);
        return $classInstantiator;
    }
}