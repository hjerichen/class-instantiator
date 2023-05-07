<?php declare(strict_types=1);

namespace HJerichen\ClassInstantiator\Test\Unit;

use HJerichen\ClassInstantiator\ClassInstantiator;
use HJerichen\ClassInstantiator\ClassInstantiatorContainer;
use HJerichen\ClassInstantiator\Test\Helpers\ClassWithTwoIntegerParameters;
use HJerichen\ClassInstantiator\Test\Helpers\SimpleClass;
use HJerichen\ClassInstantiator\Test\TestCase;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

class ClassInstantiatorContainerTest extends TestCase
{
    private ClassInstantiatorContainer $container;

    protected function setUp(): void
    {
        parent::setUp();

        $instantiator = new ClassInstantiator();
        $this->container = new ClassInstantiatorContainer($instantiator);
    }


    /* TESTS */

    public function testClassImplementsCorrectInterface(): void
    {
        self::assertInstanceOf(ContainerInterface::class, $this->container);
    }

    public function testHasForExistingClass(): void
    {
        $actual = $this->container->has(SimpleClass::class);
        self::assertTrue($actual);
    }

    public function testHasForNotExistingClass(): void
    {
        $actual = $this->container->has('SomeClassDoesNotExist');
        self::assertFalse($actual);
    }

    public function testGetForInstantiableClass(): void
    {
        $expected = new SimpleClass();
        $actual = $this->container->get(SimpleClass::class);
        self::assertEquals($expected, $actual);
    }

    public function testGetReturnsSameInstanceOnSecondCall(): void
    {
        $expected = $this->container->get(SimpleClass::class);
        $actual = $this->container->get(SimpleClass::class);
        self::assertSame($expected, $actual);
    }

    /**
     * @psalm-suppress ArgumentTypeCoercion Is testet.
     * @psalm-suppress UndefinedClass Is testet.
     */
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
