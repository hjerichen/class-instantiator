<?php

namespace HJerichen\ObjectFactory;

use HJerichen\ObjectFactory\Exception\UnknownClassException;
use ReflectionClass;
use ReflectionException;

/**
 * @author Heiko Jerichen <heiko@jerichen.de>
 */
class ClassInstantiator
{
    public function instantiateClass(string $class, array $predefinedArguments = []): object
    {
        try {
            $reflectionClass = new ReflectionClass($class);
        } catch (ReflectionException $exception) {
            throw new UnknownClassException($class);
        }
        return $this->instantiateClassFromReflection($reflectionClass, $predefinedArguments);
    }

    private function instantiateClassFromReflection(ReflectionClass $class, $predefinedArguments): object
    {
        $classInstantiatorFromReflection = new ClassInstantiatorFromReflection($this, $class);
        return $classInstantiatorFromReflection->instantiateClass($predefinedArguments);
    }
}