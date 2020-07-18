<?php

namespace HJerichen\ClassInstantiator\Test\Unit;

use HJerichen\ClassInstantiator\ClassInstantiator;
use HJerichen\ClassInstantiator\ClassInstantiatorContainer;
use HJerichen\ClassInstantiator\Test\Helpers\ClassWithTwoIntegerParameters;
use HJerichen\ClassInstantiator\Test\Helpers\SimpleClass;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

class ClassInstantiatorContainerTest extends TestCase
{
    /** @var ClassInstantiatorContainer */
    private $container;
    /** @var ClassInstantiator */
    private $instantiator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->instantiator = new ClassInstantiator();
        $this->container = new ClassInstantiatorContainer($this->instantiator);
    }


    /* TESTS */

    public function testClassImplementsCorrectInterface(): void
    {
        $this->assertInstanceOf(ContainerInterface::class, $this->container);
    }

    public function testHasForExistingClass(): void
    {
        $expected = true;
        $actual = $this->container->has(SimpleClass::class);
        $this->assertEquals($expected, $actual);
    }

    public function testHasForNotExistingClass(): void
    {
        $expected = false;
        $actual = $this->container->has('SomeClassDoesNotExist');
        $this->assertEquals($expected, $actual);
    }

    public function testGetForInstantiableClass(): void
    {
        $expected = new SimpleClass();
        $actual = $this->container->get(SimpleClass::class);
        $this->assertEquals($expected, $actual);
    }

    public function testGetReturnsSameInstanceOnSecondCall(): void
    {
        $expected = $this->container->get(SimpleClass::class);
        $actual = $this->container->get(SimpleClass::class);
        $this->assertSame($expected, $actual);
    }

    public function testGetThrowsNotFoundExceptionForNotExistingClasses(): void
    {
        $this->expectException(NotFoundExceptionInterface::class);

        $this->container->get('SomeClassDoesNotExist');
    }

    public function testGetForNotInstantiableClass(): void
    {
        $this->expectException(ContainerExceptionInterface::class);

        $this->container->get(ClassWithTwoIntegerParameters::class);
    }
}
