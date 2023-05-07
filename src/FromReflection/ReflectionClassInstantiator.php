<?php declare(strict_types=1);

namespace HJerichen\ClassInstantiator\FromReflection;

use ReflectionClass;

/**
 * @author Heiko Jerichen <heiko@jerichen.de>
 */
interface ReflectionClassInstantiator
{
    /**
     * @template T of object
     * @param ReflectionClass<T> $reflectionClass
     * @param array<string,mixed> $predefinedArguments
     * @return T|null
     * @noinspection PhpDocSignatureInspection
     */
    public function instantiateClass(ReflectionClass $reflectionClass, array $predefinedArguments): ?object;
}