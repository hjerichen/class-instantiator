<?php /** @noinspection PhpPropertyOnlyWrittenInspection */
declare(strict_types=1);

namespace HJerichen\ClassInstantiator\Test\Helpers;

/**
 * @author Heiko Jerichen <heiko@jerichen.de>
 * @psalm-suppress UnusedProperty
 */
class ClassWithTwoSimpleDependencies
{
    public function __construct(
        private readonly SimpleClass $simpleClass,
        private readonly ClassWithSimpleDependency $classWithDependency
    ) {
    }
}