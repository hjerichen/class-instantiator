<?php /** @noinspection PhpPropertyOnlyWrittenInspection */
declare(strict_types=1);

namespace HJerichen\ClassInstantiator\Test\Helpers;

/**
 * @author Heiko Jerichen <heiko@jerichen.de>
 * @psalm-suppress UnusedProperty
 */
class ClassWithMixedParameters
{
    public function __construct(
        private readonly int $integer,
        private readonly string $string,
        private readonly array $array,
        private readonly SimpleClass $object
    ) {
    }
}