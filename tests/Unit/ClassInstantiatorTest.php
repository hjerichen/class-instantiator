<?php declare(strict_types=1);

namespace HJerichen\ClassInstantiator\Test\Unit;

use HJerichen\ClassInstantiator\ClassInstantiator;
use HJerichen\ClassInstantiator\Exception\InstantiateParameterException;
use HJerichen\ClassInstantiator\Exception\InstantiatorAnnotationException;
use HJerichen\ClassInstantiator\Exception\UnknownClassException;
use HJerichen\ClassInstantiator\Test\Helpers\ClassInstantiatorExtended;
use HJerichen\ClassInstantiator\Test\Helpers\ClassWithDependencyOfEnvironment;
use HJerichen\ClassInstantiator\Test\Helpers\ClassWithDependencyOfEnvironmentWithAnnotation;
use HJerichen\ClassInstantiator\Test\Helpers\ClassWithDependencyOfEnvironmentWithNotInstantiatorInAnnotation;
use HJerichen\ClassInstantiator\Test\Helpers\ClassWithDependencyOfEnvironmentWithUnknownAnnotation;
use HJerichen\ClassInstantiator\Test\Helpers\ClassWithDependencyOfEnvironmentWithWrongAnnotation;
use HJerichen\ClassInstantiator\Test\Helpers\ClassWithDependencyOfIntegerClass;
use HJerichen\ClassInstantiator\Test\Helpers\ClassWithDependencyOfInterface;
use HJerichen\ClassInstantiator\Test\Helpers\ClassWithIntegerParameter;
use HJerichen\ClassInstantiator\Test\Helpers\ClassWithMixedParameters;
use HJerichen\ClassInstantiator\Test\Helpers\ClassWithSimpleDependency;
use HJerichen\ClassInstantiator\Test\Helpers\ClassWithTwoIntegerParameters;
use HJerichen\ClassInstantiator\Test\Helpers\ClassWithTwoSimpleDependencies;
use HJerichen\ClassInstantiator\Test\Helpers\Environment;
use HJerichen\ClassInstantiator\Test\Helpers\SimpleClass;
use HJerichen\ClassInstantiator\Test\Helpers\SomeInterface;
use HJerichen\ClassInstantiator\Test\Helpers\SomeInterfaceImplementation;
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
        $class = ClassWithDependencyOfEnvironment::class;

        $expected = new $class(new Environment(4));
        $actual = $this->classInstantiatorExtended->instantiateClass($class);
        $this->assertEquals($expected, $actual);
    }

    public function testInstantiateHimself(): void
    {
        $expected = new ClassInstantiator();
        $actual = $this->classInstantiator->instantiateClass(ClassInstantiator::class);
        $this->assertEquals($expected, $actual);
    }

    public function testInstantiateWithAnnotation(): void
    {
        $class = ClassWithDependencyOfEnvironmentWithAnnotation::class;

        $expected = new $class(new Environment(4));
        $actual = $this->classInstantiator->instantiateClass($class);
        $this->assertEquals($expected, $actual);
    }

    public function testInstantiateWithWrongAnnotation(): void
    {
        $class = ClassWithDependencyOfEnvironmentWithWrongAnnotation::class;

        $this->expectException(InstantiateParameterException::class);

        $this->classInstantiator->instantiateClass($class);
    }

    public function testInstantiateWithUnknownClassInAnnotation(): void
    {
        $class = ClassWithDependencyOfEnvironmentWithUnknownAnnotation::class;

        $this->expectException(InstantiatorAnnotationException::class);
        $this->expectExceptionMessage('Invalid value for Annotation "Instantiator": ');

        $this->classInstantiator->instantiateClass($class);
    }

    public function testInstantiateWithNotInstantiatorClassInAnnotation(): void
    {
        $class = ClassWithDependencyOfEnvironmentWithNotInstantiatorInAnnotation::class;

        $this->expectException(InstantiatorAnnotationException::class);
        $this->expectExceptionMessage('Invalid value for Annotation "Instantiator": Value in class HJerichen\ClassInstantiator\Test\Helpers\ClassWithDependencyOfEnvironmentWithNotInstantiatorInAnnotation is not an instance of ClassInstantiator');

        $this->classInstantiator->instantiateClass($class);
    }


    /* HELPERS */
}
