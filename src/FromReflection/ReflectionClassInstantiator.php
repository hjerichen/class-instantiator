<?php declare(strict_types=1);

namespace HJerichen\ClassInstantiator\FromReflection;

use ReflectionClass;

/**
 * @author Heiko Jerichen <heiko@jerichen.de>
 */
interface ReflectionClassInstantiator
{
    public function instantiateClass(ReflectionClass $reflectionClass, array $predefinedArguments): ?object;
}