<?php /** @noinspection PhpPropertyOnlyWrittenInspection */
declare(strict_types=1);

namespace HJerichen\ClassInstantiator\Test\Helpers;

use HJerichen\ClassInstantiator\Annotation\Instantiator;
use HJerichen\ClassInstantiator\ClassInstantiator;

/**
 * @Instantiator(ClassInstantiator::class)
 * @author Heiko Jerichen <heiko@jerichen.de>
 */
class ClassWithDependencyOfEnvironmentWithWrongAnnotation
{
    public function __construct(
        private readonly Environment $environment
    ) {
    }
}