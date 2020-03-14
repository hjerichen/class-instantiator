<?php

namespace HJerichen\ClassInstantiator\TestHelpers;

/**
 * @author Heiko Jerichen <heiko@jerichen.de>
 */
class Environment
{
    /**
     * @var int
     */
    private $value;

    /** @noinspection UnusedConstructorDependenciesInspection */
    public function __construct(int $value)
    {
        $this->value = $value;
    }
}