<?php declare(strict_types=1);

namespace HJerichen\ClassInstantiator\Test\Helpers;

/**
 * @author Heiko Jerichen <heiko@jerichen.de>
 */
class ClassWithSimpleDependency
{
    private SimpleClass $simpleClass;

    /** @noinspection UnusedConstructorDependenciesInspection */
    public function __construct(SimpleClass $simpleClass)
    {
        $this->simpleClass = $simpleClass;
    }


}