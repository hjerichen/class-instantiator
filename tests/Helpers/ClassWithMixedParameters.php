<?php /** @noinspection PhpPropertyOnlyWrittenInspection */
declare(strict_types=1);

namespace HJerichen\ClassInstantiator\Test\Helpers;

/**
 * @author Heiko Jerichen <heiko@jerichen.de>
 * @psalm-suppress UnusedProperty
 */
readonly class ClassWithMixedParameters
{
    public function __construct(
        private int $integer,
        private string $string,
        private array $array,
        private SimpleClass $object
    ) {
    }
}