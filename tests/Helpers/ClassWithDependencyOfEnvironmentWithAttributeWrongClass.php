<?php /** @noinspection PhpUnused */
declare(strict_types=1);

namespace HJerichen\ClassInstantiator\Test\Helpers;

use HJerichen\ClassInstantiator\Attribute\Instantiator;

/**
 * @author Heiko Jerichen <heiko@jerichen.de>
 * @psalm-api
 */
#[Instantiator(class: SimpleClass::class, store: true)]
readonly class ClassWithDependencyOfEnvironmentWithAttributeWrongClass
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