<?php

namespace HJerichen\ObjectFactory\TestHelpers;

/**
 * @author Heiko Jerichen <heiko@jerichen.de>
 */
class ClassWithSimpleDependency
{
    /**
     * @var SimpleClass
     */
    private $simpleClass;

    /** @noinspection UnusedConstructorDependenciesInspection */
    public function __construct(SimpleClass $simpleClass)
    {
        $this->simpleClass = $simpleClass;
    }


}