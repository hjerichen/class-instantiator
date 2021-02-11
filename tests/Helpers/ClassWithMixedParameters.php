<?php declare(strict_types=1);

namespace HJerichen\ClassInstantiator\Test\Helpers;

/**
 * @author Heiko Jerichen <heiko@jerichen.de>
 */
class ClassWithMixedParameters
{
    private int $integer;
    private string $string;
    private array $array;
    private SimpleClass $object;

    /** @noinspection UnusedConstructorDependenciesInspection */
    public function __construct(int $integer, string $string, array $array, SimpleClass $object)
    {
        $this->integer = $integer;
        $this->string = $string;
        $this->array = $array;
        $this->object = $object;
    }
}