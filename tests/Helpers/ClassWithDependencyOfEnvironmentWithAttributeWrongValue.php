<?php
/** @noinspection PhpUnusedAliasInspection */
declare(strict_types=1);

namespace HJerichen\ClassInstantiator\Test\Helpers;

use HJerichen\ClassInstantiator\Attribute\Instantiator;
use HJerichen\ClassInstantiator\Test\Helpers\ClassInstantiatorExtended;

/**
 * @author Heiko Jerichen <heiko@jerichen.de>
 */
#[Instantiator(class: SimpleClass::class, store: 'test')]
class ClassWithDependencyOfEnvironmentWithAttributeWrongValue
{
    private Environment $environment;

    public function __construct(Environment $environment)
    {
        $this->environment = $environment;
    }

    public function getEnvironment(): Environment
    {
        return $this->environment;
    }
}