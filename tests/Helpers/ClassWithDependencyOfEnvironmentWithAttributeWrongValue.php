<?php
/** @noinspection PhpStrictTypeCheckingInspection */
/** @noinspection PhpUnused */
declare(strict_types=1);

namespace HJerichen\ClassInstantiator\Test\Helpers;

use HJerichen\ClassInstantiator\Attribute\Instantiator;

/**
 * @author Heiko Jerichen <heiko@jerichen.de>
 */
#[Instantiator(class: SimpleClass::class, store: 'test')]
class ClassWithDependencyOfEnvironmentWithAttributeWrongValue
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