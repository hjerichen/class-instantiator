<?php
/** @noinspection PhpUnusedAliasInspection */
declare(strict_types=1);

namespace HJerichen\ClassInstantiator\Test\Helpers;

use HJerichen\ClassInstantiator\Annotation\Instantiator;
use HJerichen\ClassInstantiator\Test\Helpers\ClassInstantiatorExtended;

/**
 * @Instantiator(ClassInstantiatorExtended::class)
 * @author Heiko Jerichen <heiko@jerichen.de>
 */
class ClassWithDependencyOfEnvironmentWithAnnotation
{
    /** @var Environment */
    private $environment;

    public function __construct(Environment $environment)
    {
        $this->environment = $environment;
    }

    public function getEnvironment(): Environment
    {
        return $this->environment;
    }
}