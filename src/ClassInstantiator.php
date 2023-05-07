<?php declare(strict_types=1);

namespace HJerichen\ClassInstantiator;

use HJerichen\ClassInstantiator\FromReflection\ReflectionClassInstantiator;
use HJerichen\ClassInstantiator\FromReflection\ReflectionClassInstantiatorBase;
use HJerichen\ClassInstantiator\Exception\UnknownClassException;
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

    /**
     * @template T of object
     * @param class-string<T> $class
     * @param array<string,mixed> $predefinedArguments
     * @return T
     * @noinspection PhpDocSignatureInspection
     */
    public function instantiateClass(string $class, array $predefinedArguments = []): object
    {
        $reflectionClass = $this->reflectClass($class);
        return $this->instantiateClassFromReflection($reflectionClass, $predefinedArguments);
    }

    /**
     * @template T of object
     * @param  ReflectionClass<T> $class
     * @param  array<string,mixed> $predefinedArguments
     * @return   T
     * @noinspection PhpDocSignatureInspection
     */
    public function instantiateClassFromReflection(ReflectionClass $class, array $predefinedArguments = []): object
    {
        /** @noinspection OneTimeUseVariablesInspection */
        $reflectionClassInstantiator = $this->createReflectionClassInstantiator();
        return $reflectionClassInstantiator->instantiateClass($class, $predefinedArguments);
    }

    /** @param class-string|null $class */
    public function injectObject(object $object, ?string $class = null): void
    {
        $this->objectStore->storeObject($object, $class);
    }

    protected function createReflectionClassInstantiator(): ReflectionClassInstantiator
    {
        $classInstantiator = new ReflectionClassInstantiatorBase($this);
        $classInstantiator = new ReflectionClassInstantiatorWithAttribute($classInstantiator, $this, $this->objectStore);
        $classInstantiator = new ReflectionClassInstantiatorWithExtension($classInstantiator, $this);
        $classInstantiator = new ReflectionClassInstantiatorWithObjectStore($classInstantiator, $this->objectStore);
        return $classInstantiator;
    }

    /**
     * @template T of object
     * @param class-string<T> $class
     * @return ReflectionClass<T>
     */
    private function reflectClass(string $class): ReflectionClass
    {
        try {
            return new ReflectionClass($class);
        } catch (ReflectionException) {
            throw new UnknownClassException($class);
        }
    }
}