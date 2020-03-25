<?php declare(strict_types=1);

namespace HJerichen\ClassInstantiator;

use HJerichen\ClassInstantiator\Exception\InstantiateParameterException;
use HJerichen\ClassInstantiator\Exception\UnknownClassException;
use HJerichen\ClassInstantiator\TestHelpers\ClassInstantiatorExtended;
use HJerichen\ClassInstantiator\TestHelpers\ClassWithDependencyOfEnvironment;
use HJerichen\ClassInstantiator\TestHelpers\ClassWithDependencyOfIntegerClass;
use HJerichen\ClassInstantiator\TestHelpers\ClassWithDependencyOfInterface;
use HJerichen\ClassInstantiator\TestHelpers\ClassWithIntegerParameter;
use HJerichen\ClassInstantiator\TestHelpers\ClassWithMixedParameters;
use HJerichen\ClassInstantiator\TestHelpers\ClassWithSimpleDependency;
use HJerichen\ClassInstantiator\TestHelpers\ClassWithTwoIntegerParameters;
use HJerichen\ClassInstantiator\TestHelpers\ClassWithTwoSimpleDependencies;
use HJerichen\ClassInstantiator\TestHelpers\Environment;
use HJerichen\ClassInstantiator\TestHelpers\SimpleClass;
use HJerichen\ClassInstantiator\TestHelpers\SomeInterface;
use HJerichen\ClassInstantiator\TestHelpers\SomeInterfaceImplementation;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

/**
 * @author Heiko Jerichen <heiko@jerichen.de>
 */
class ClassInstantiatorTest extends TestCase
{
    /**
     * @var ClassInstantiator
     */
    private $classInstantiator;
    /**
     * @var ClassInstantiatorExtended
     */
    private $classInstantiatorExtended;

    /**
     *
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->classInstantiator = new ClassInstantiator();
        $this->classInstantiatorExtended = new ClassInstantiatorExtended();
    }


    /* TESTS */

    public function testInstantiatingSimpleClass(): void
    {
        $expected = new SimpleClass();
        $actual = $this->classInstantiator->instantiateClass(SimpleClass::class);
        $this->assertEquals($expected, $actual);
    }

    public function testInstantiateClassWithSimpleDependency(): void
    {
        $expected = new ClassWithSimpleDependency(new SimpleClass());
        $actual = $this->classInstantiator->instantiateClass(ClassWithSimpleDependency::class);
        $this->assertEquals($expected, $actual);
    }

    public function testInstantiateClassWithMoreTheOneSimpleDependency(): void
    {
        $expected = new ClassWithTwoSimpleDependencies(new SimpleClass(), new ClassWithSimpleDependency(new SimpleClass()));
        $actual = $this->classInstantiator->instantiateClass(ClassWithTwoSimpleDependencies::class);
        $this->assertEquals($expected, $actual);
    }

    public function testInstantiateNotExistingClass(): void
    {
        $class = 'Test';
        $exception = new UnknownClassException($class);

        $this->expectExceptionObject($exception);
        $this->classInstantiator->instantiateClass($class);
    }

    public function testInstantiateClassWithIntegerParameterWithoutProvidedArguments(): void
    {
        $reflectionClass = new ReflectionClass(ClassWithIntegerParameter::class);
        $reflectionParameter = $reflectionClass->getConstructor()->getParameters()[0];
        $exception = new InstantiateParameterException($reflectionParameter);

        $this->expectExceptionObject($exception);
        $this->classInstantiator->instantiateClass(ClassWithIntegerParameter::class);
    }

    public function testInstantiateClassWithTwoIntegerParametersWithProvidedNamedArguments(): void
    {
        $arguments = [
            'value2' => 10,
            'value1' => 5
        ];

        $expected = new ClassWithTwoIntegerParameters($arguments['value1'], $arguments['value2']);
        $actual = $this->classInstantiator->instantiateClass(ClassWithTwoIntegerParameters::class, $arguments);
        $this->assertEquals($expected, $actual);
    }

    public function testInstantiateClassWithTwoIntegerParametersWithProvidedNamedArgumentsRecursive(): void
    {
        $arguments = [
            'value' => 10,
            'integer' => 5
        ];

        $expected = new ClassWithDependencyOfIntegerClass($arguments['integer'], new ClassWithIntegerParameter($arguments['value']));
        $actual = $this->classInstantiator->instantiateClass(ClassWithDependencyOfIntegerClass::class, $arguments);
        $this->assertEquals($expected, $actual);
    }

    public function testInstantiateClassWithMixedParametersWithFirstProvidedArguments(): void
    {
        $arguments = [
            'integer' => 5,
            'string' => 'test',
            'array' => ['name' => 'jon']
        ];
        $object = new SimpleClass();

        $expected = new ClassWithMixedParameters($arguments['integer'], $arguments['string'], $arguments['array'], $object);
        $actual = $this->classInstantiator->instantiateClass(ClassWithMixedParameters::class, $arguments);
        $this->assertEquals($expected, $actual);
    }

    public function testInstantiateClassWithMethodOfExtension(): void
    {
        $expected = new ClassWithIntegerParameter(5);
        $actual = $this->classInstantiatorExtended->instantiateClass(ClassWithIntegerParameter::class);
        $this->assertEquals($expected, $actual);
    }

    public function testInstantiateClassWithMethodOfExtensionHasParameter(): void
    {
        $arguments = [
            'value' => 4
        ];

        $expected = new ClassWithTwoIntegerParameters(5, $arguments['value']);
        $actual = $this->classInstantiatorExtended->instantiateClass(ClassWithTwoIntegerParameters::class, $arguments);
        $this->assertEquals($expected, $actual);
    }

    public function testInstantiateInterfaceWithMethodOfExtension(): void
    {
        $expected = new SomeInterfaceImplementation();
        $actual = $this->classInstantiatorExtended->instantiateClass(SomeInterface::class);
        $this->assertEquals($expected, $actual);
    }

    public function testInstantiateClassWithInterfaceDependency(): void
    {
        $expected = new ClassWithDependencyOfInterface(new SomeInterfaceImplementation());
        $actual = $this->classInstantiatorExtended->instantiateClass(ClassWithDependencyOfInterface::class);
        $this->assertEquals($expected, $actual);
    }

    public function testInstantiateClassWithSetProperty(): void
    {
        $expected = new ClassWithDependencyOfEnvironment(new Environment(4));
        $actual = $this->classInstantiatorExtended->instantiateClass(ClassWithDependencyOfEnvironment::class);
        $this->assertEquals($expected, $actual);
    }

    public function testInstantiateHimself(): void
    {
        $expected = new ClassInstantiator();
        $actual = $this->classInstantiator->instantiateClass(ClassInstantiator::class);
        $this->assertEquals($expected, $actual);
    }


    /* HELPERS */
}
