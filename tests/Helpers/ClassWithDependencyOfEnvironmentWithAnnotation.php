<?php
declare(strict_types=1);

namespace HJerichen\ClassInstantiator\Test\Helpers;

use HJerichen\ClassInstantiator\Annotation\Instantiator;

/**
 * @Instantiator(ClassInstantiatorExtended::class)
 * @author Heiko Jerichen <heiko@jerichen.de>
 */
class ClassWithDependencyOfEnvironmentWithAnnotation
{
    public function __construct(
        private readonly Environment $environment
    ) {
    }

    public function getEnvironment(): Environment
    {
        return $this->environment;
    }
}