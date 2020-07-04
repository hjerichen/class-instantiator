<?php declare(strict_types=1);

namespace HJerichen\ClassInstantiator\Test\Helpers;

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