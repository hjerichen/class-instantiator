<?php declare(strict_types=1);

namespace HJerichen\ClassInstantiator;

use HJerichen\ClassInstantiator\FromReflection\ReflectionClassInstantiator;
use HJerichen\ClassInstantiator\FromReflection\ReflectionClassInstantiatorBase;
use HJerichen\ClassInstantiator\Exception\UnknownClassException;
use HJerichen\ClassInstantiator\FromReflection\ReflectionClassInstantiatorWithAnnotation;
use HJerichen\ClassInstantiator\FromReflection\ReflectionClassInstantiatorWithAttribute;
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
        $reflectionClass = $this->reflectClass($class);
        return $this->instantiateClassFromReflection($reflectionClass, $predefinedArguments);
    }

    public function instantiateClassFromReflection(ReflectionClass $class, $predefinedArguments = []): object
    {
        /** @noinspection OneTimeUseVariablesInspection */
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
        if ($this->attributesAreSupported()) {
            $classInstantiator = new ReflectionClassInstantiatorWithAttribute($classInstantiator, $this, $this->objectStore);
        }
        $classInstantiator = new ReflectionClassInstantiatorWithAnnotation($classInstantiator, $this, $this->objectStore);
        $classInstantiator = new ReflectionClassInstantiatorWithExtension($classInstantiator, $this);
        $classInstantiator = new ReflectionClassInstantiatorWithObjectStore($classInstantiator, $this->objectStore);
        return $classInstantiator;
    }

    private function reflectClass(string $class): ReflectionClass
    {
        try {
            return new ReflectionClass($class);
        } catch (ReflectionException) {
            throw new UnknownClassException($class);
        }
    }

    private function attributesAreSupported(): bool
    {
        $phpversion = (int)PHP_VERSION;
        return $phpversion >= 8;
    }
}