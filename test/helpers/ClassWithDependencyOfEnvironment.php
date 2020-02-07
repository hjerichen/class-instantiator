<?php

namespace HJerichen\ObjectFactory\TestHelpers;

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