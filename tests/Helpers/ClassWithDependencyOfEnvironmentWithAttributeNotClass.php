<?php
/** @noinspection PhpUnused */
declare(strict_types=1);

namespace HJerichen\ClassInstantiator\Test\Helpers;

use HJerichen\ClassInstantiator\Attribute\Instantiator;

/**
 * @author Heiko Jerichen <heiko@jerichen.de>
 * @psalm-suppress ArgumentTypeCoercion
 * @psalm-api
 */
#[Instantiator(class: 'test', store: true)]
readonly class ClassWithDependencyOfEnvironmentWithAttributeNotClass
{
    public function __construct(
        private Environment $environment
    ) {
    }

    public function getEnvironment(): Environment
    {
        return $this->environment;
    }
}