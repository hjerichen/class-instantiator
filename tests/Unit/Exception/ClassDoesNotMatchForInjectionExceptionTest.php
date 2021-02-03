<?php

namespace HJerichen\ClassInstantiator\Test\Unit\Exception;

use HJerichen\ClassInstantiator\Exception\ClassDoesNotMatchForInjectionException;
use HJerichen\ClassInstantiator\Test\Helpers\Environment;
use HJerichen\ClassInstantiator\Test\Helpers\SomeInterface;
use HJerichen\ClassInstantiator\Test\TestCase;

/**
 * @author Heiko Jerichen <heiko@jerichen.de>
 */
class ClassDoesNotMatchForInjectionExceptionTest extends TestCase
{
    public function testMessage(): void
    {
        $class = SomeInterface::class;
        $object = new Environment(10);
        $objectClass = Environment::class;

        $exception = new ClassDoesNotMatchForInjectionException($object, $class);

        $expected = "Object of class {$objectClass} is not instance of {$class}";
        $actual = $exception->getMessage();
        self::assertEquals($expected, $actual);
    }
}