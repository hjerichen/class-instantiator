<?php declare(strict_types=1);

namespace HJerichen\ClassInstantiator\TestHelpers;

/**
 * @author Heiko Jerichen <heiko@jerichen.de>
 */
class ClassWithDependencyOfEnvironment
{
    /**
     * @var Environment
     */
    private $environment;

    /** @noinspection UnusedConstructorDependenciesInspection */
    public function __construct(Environment $environment)
    {
        $this->environment = $environment;
    }
}