<?php

namespace HJerichen\ObjectFactory\TestHelpers;

/**
 * @author Heiko Jerichen <heiko@jerichen.de>
 */
class ClassWithMixedParameters
{
    /**
     * @var int
     */
    private $integer;
    /**
     * @var string
     */
    private $string;
    /**
     * @var array
     */
    private $array;
    /**
     * @var SimpleClass
     */
    private $object;

    /** @noinspection UnusedConstructorDependenciesInspection */
    public function __construct(int $integer, string $string, array $array, SimpleClass $object)
    {
        $this->integer = $integer;
        $this->string = $string;
        $this->array = $array;
        $this->object = $object;
    }
}