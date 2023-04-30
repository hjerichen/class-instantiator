<?php /** @noinspection PhpPropertyOnlyWrittenInspection */
declare(strict_types=1);

namespace HJerichen\ClassInstantiator\Test\Helpers;

/**
 * @author Heiko Jerichen <heiko@jerichen.de>
 */
class ClassWithTwoIntegerParameters
{
    public function __construct(
        private readonly int $value1,
        private readonly int $value2
    ) {
    }
}