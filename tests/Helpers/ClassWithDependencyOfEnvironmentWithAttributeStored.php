<?php /** @noinspection PhpUnused */
declare(strict_types=1);

namespace HJerichen\ClassInstantiator\Test\Helpers;

use HJerichen\ClassInstantiator\Attribute\Instantiator;

/**
 * @author Heiko Jerichen <heiko@jerichen.de>
 * @psalm-api
 */
#[Instantiator(class: ClassInstantiatorExtended::class, store: true)]
class ClassWithDependencyOfEnvironmentWithAttributeStored
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