<?php declare(strict_types=1);

namespace HJerichen\ClassInstantiator\Test\Helpers;

/**
 * @author Heiko Jerichen <heiko@jerichen.de>
 */
class ClassWithDependencyOfInterface
{
    private SomeInterface $someInterface;

    /** @noinspection UnusedConstructorDependenciesInspection */
    public function __construct(SomeInterface $someInterface)
    {
        $this->someInterface = $someInterface;
    }
}