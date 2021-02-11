<?php declare(strict_types=1);

namespace HJerichen\ClassInstantiator\Test\Helpers;

/**
 * @author Heiko Jerichen <heiko@jerichen.de>
 */
class SomeInterface2Implementation implements SomeInterface2
{
    private int $id;

    /** @noinspection UnusedConstructorDependenciesInspection */
    public function __construct(int $id)
    {
        $this->id = $id;
    }
}