<?php declare(strict_types=1);

namespace HJerichen\ClassInstantiator\Test\Unit;

use HJerichen\ClassInstantiator\ClassInstantiator;
use HJerichen\ClassInstantiator\Exception\ClassDoesNotMatchForInjectionException;
use HJerichen\ClassInstantiator\Exception\InstantiateParameterException;
use HJerichen\ClassInstantiator\Exception\InstantiatorAttributeException;
use HJerichen\ClassInstantiator\Exception\UnknownClassException;
use HJerichen\ClassInstantiator\Test\Helpers\ClassForExtensionHasHigherPriorityThenAttribute;
use HJerichen\ClassInstantiator\Test\Helpers\ClassForExtensionHasHigherPriorityThenAttribute2;
use HJerichen\ClassInstantiator\Test\Helpers\ClassInstantiatorExtended;
use HJerichen\ClassInstantiator\Test\Helpers\ClassWithDependencyOfEnvironment;
use HJerichen\ClassInstantiator\Test\Helpers\ClassWithDependencyOfEnvironmentWithAttribute;
use HJerichen\ClassInstantiator\Test\Helpers\ClassWithDependencyOfEnvironmentWithAttributeNotClass;
use HJerichen\ClassInstantiator\Test\Helpers\ClassWithDependencyOfEnvironmentWithAttributeStored;
use HJerichen\ClassInstantiator\Test\Helpers\ClassWithDependencyOfEnvironmentWithAttributeWrongClass;
use HJerichen\ClassInstantiator\Test\Helpers\ClassWithDependencyOfEnvironmentWithAttributeWrongValue;
use HJerichen\ClassInstantiator\Test\Helpers\ClassWithDependencyOfIntegerClass;
use HJerichen\ClassInstantiator\Test\Helpers\ClassWithDependencyOfIntegerClass2;
use HJerichen\ClassInstantiator\Test\Helpers\ClassWithDependencyOfIntegerClass3;
use HJerichen\ClassInstantiator\Test\Helpers\ClassWithDependencyOfIntegerClass4;
use HJerichen\ClassInstantiator\Test\Helpers\ClassWithDependencyOfIntegerClass5;
use HJerichen\ClassInstantiator\Test\Helpers\ClassWithDependencyOfInterface;
use HJerichen\ClassInstantiator\Test\Helpers\ClassWithIntegerParameter;
use HJerichen\ClassInstantiator\Test\Helpers\ClassWithMixedParameters;
use HJerichen\ClassInstantiator\Test\Helpers\ClassWithOnlyStore;
use HJerichen\ClassInstantiator\Test\Helpers\ClassWithSimpleDependency;
use HJerichen\ClassInstantiator\Test\Helpers\ClassWithTwoIntegerParameters;
use HJerichen\ClassInstantiator\Test\Helpers\ClassWithTwoSimpleDependencies;
use HJerichen\ClassInstantiator\Test\Helpers\Environment;
use HJerichen\ClassInstantiator\Test\Helpers\InterfaceToStore;
use HJerichen\ClassInstantiator\Test\Helpers\SimpleClass;
use HJerichen\ClassInstantiator\Test\Helpers\SomeInterface;
use HJerichen\ClassInstantiator\Test\Helpers\SomeInterface2;
use HJerichen\ClassInstantiator\Test\Helpers\SomeInterface2Implementation;
use HJerichen\ClassInstantiator\Test\Helpers\SomeInterfaceImplementation;
use HJerichen\ClassInstantiator\Test\TestCase;
use ReflectionClass;

/**
 * @author Heiko Jerichen <heiko@jerichen.de>
 */
class ClassInstantiatorTest extends TestCase
{
    private ClassInstantiator $classInstantiator;
    private ClassInstantiatorExtended $classInstantiatorExtended;

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
        self::assertEquals($expected, $actual);
    }

    public function testInstantiateClassWithSimpleDependency(): void
    {
        $expected = new ClassWithSimpleDependency(new SimpleClass());
        $actual = $this->classInstantiator->instantiateClass(ClassWithSimpleDependency::class);
        self::assertEquals($expected, $actual);
    }

    public function testInstantiateClassWithMoreTheOneSimpleDependency(): void
    {
        $expected = new ClassWithTwoSimpleDependencies(new SimpleClass(), new ClassWithSimpleDependency(new SimpleClass()));
        $actual = $this->classInstantiator->instantiateClass(ClassWithTwoSimpleDependencies::class);
        self::assertEquals($expected, $actual);
    }

    /**
     * @psalm-suppress ArgumentTypeCoercion Is testet.
     */
    public function testInstantiateNotExistingClass(): void
    {
        $class = 'Test';
        $exception = new UnknownClassException($class);

        $this->expectExceptionObject($exception);
        $this->classInstantiator->instantiateClass($class);
    }

    /** @psalm-suppress PossiblyNullReference */
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
        self::assertEquals($expected, $actual);
    }

    public function testInstantiateClassWithTwoIntegerParametersWithProvidedNamedArgumentsRecursive(): void
    {
        $arguments = [
            'value' => 10,
            'integer' => 5
        ];

        $expected = new ClassWithDependencyOfIntegerClass($arguments['integer'], new ClassWithIntegerParameter($arguments['value']));
        $actual = $this->classInstantiator->instantiateClass(ClassWithDependencyOfIntegerClass::class, $arguments);
        self::assertEquals($expected, $actual);
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
        self::assertEquals($expected, $actual);
    }

    public function testInstantiateClassWithMethodOfExtension(): void
    {
        $expected = new ClassWithIntegerParameter(5);
        $actual = $this->classInstantiatorExtended->instantiateClass(ClassWithIntegerParameter::class);
        self::assertEquals($expected, $actual);
    }

    public function testInstantiateClassWithMethodOfExtensionHasParameter(): void
    {
        $arguments = [
            'value' => 4
        ];

        $expected = new ClassWithTwoIntegerParameters(5, $arguments['value']);
        $actual = $this->classInstantiatorExtended->instantiateClass(ClassWithTwoIntegerParameters::class, $arguments);
        self::assertEquals($expected, $actual);
    }

    public function testInstantiateInterfaceWithMethodOfExtension(): void
    {
        $expected = new SomeInterfaceImplementation();
        $actual = $this->classInstantiatorExtended->instantiateClass(SomeInterface::class);
        self::assertEquals($expected, $actual);
    }

    public function testInstantiateClassWithInterfaceDependency(): void
    {
        $expected = new ClassWithDependencyOfInterface(new SomeInterfaceImplementation());
        $actual = $this->classInstantiatorExtended->instantiateClass(ClassWithDependencyOfInterface::class);
        self::assertEquals($expected, $actual);
    }

    public function testInstantiateClassWithSetProperty(): void
    {
        $class = ClassWithDependencyOfEnvironment::class;

        $expected = new $class(new Environment(4));
        $actual = $this->classInstantiatorExtended->instantiateClass($class);
        self::assertEquals($expected, $actual);
    }

    public function testInstantiateHimself(): void
    {
        $expected = new ClassInstantiator();
        $actual = $this->classInstantiator->instantiateClass(ClassInstantiator::class);
        self::assertEquals($expected, $actual);
    }

    public function testInstantiateWithAttributeOnInterface(): void
    {
        $class = SomeInterface2::class;

        $expected = new SomeInterface2Implementation(2);
        $actual = $this->classInstantiator->instantiateClass($class);
        self::assertEquals($expected, $actual);
    }

    public function testInstantiateWithAttribute(): void
    {
        if (!$this->attributesAreSupported()) {
            self::markTestSkipped('Needs php 8');
        }
        $class = ClassWithDependencyOfEnvironmentWithAttribute::class;

        $expected = new $class(new Environment(4));
        $actual = $this->classInstantiator->instantiateClass($class);
        self::assertEquals($expected, $actual);
    }

    public function testInstantiateWithAttributeNotStored(): void
    {
        if (!$this->attributesAreSupported()) {
            self::markTestSkipped('Needs php 8');
        }
        $class = ClassWithDependencyOfEnvironmentWithAttribute::class;

        $instance1 = $this->classInstantiator->instantiateClass($class);
        $instance2 = $this->classInstantiator->instantiateClass($class);
        self::assertNotSame($instance1, $instance2);
    }

    public function testInstantiateWithAttributeStored(): void
    {
        if (!$this->attributesAreSupported()) {
            self::markTestSkipped('Needs php 8');
        }
        $class = ClassWithDependencyOfEnvironmentWithAttributeStored::class;

        $instance1 = $this->classInstantiator->instantiateClass($class);
        $instance2 = $this->classInstantiator->instantiateClass($class);
        self::assertSame($instance1, $instance2);
    }

    public function testInstantiateWithAttributeHasWrongClass(): void
    {
        if (!$this->attributesAreSupported()) {
            self::markTestSkipped('Needs php 8');
        }
        $class = ClassWithDependencyOfEnvironmentWithAttributeWrongClass::class;

        $this->expectException(InstantiatorAttributeException::class);
        $this->expectExceptionMessage('Invalid value for Attribute "Instantiator": Value HJerichen\ClassInstantiator\Test\Helpers\SimpleClass is not an instance of ClassInstantiator');

        $this->classInstantiator->instantiateClass($class);
    }

    public function testInstantiateWithAttributeHasNotClass(): void
    {
        if (!$this->attributesAreSupported()) {
            self::markTestSkipped('Needs php 8');
        }
        $class = ClassWithDependencyOfEnvironmentWithAttributeNotClass::class;

        $this->expectException(UnknownClassException::class);
        $this->expectExceptionMessage('Class "test" not found.');

        $this->classInstantiator->instantiateClass($class);
    }

    public function testInstantiateWithAttributeHasWrongValue(): void
    {
        if (!$this->attributesAreSupported()) {
            self::markTestSkipped('Needs php 8');
        }
        $class = ClassWithDependencyOfEnvironmentWithAttributeWrongValue::class;

        $this->expectException(InstantiatorAttributeException::class);
        $this->expectExceptionMessage('Invalid value for Attribute "Instantiator"');

        $this->classInstantiator->instantiateClass($class);
    }

    public function testInjectObject(): void
    {
        $object = new Environment(3);
        $this->classInstantiator->injectObject($object);

        $expected = $object;
        $actual = $this->classInstantiator->instantiateClass(Environment::class);
        self::assertSame($expected, $actual);
    }

    public function testInjectObjectForInterface(): void
    {
        $object = new SomeInterfaceImplementation();
        $this->classInstantiator->injectObject($object, SomeInterface::class);

        $expected = $object;
        $actual = $this->classInstantiator->instantiateClass(SomeInterface::class);
        self::assertSame($expected, $actual);
    }

    public function testObjectIsStoredOverMultipleInstances(): void
    {
        $object = new Environment(3);
        $this->classInstantiator->injectObject($object);

        $expected = $object;
        $actual = $this->classInstantiatorExtended->instantiateClass(Environment::class);
        self::assertSame($expected, $actual);
    }

    public function testInjectObjectWithWrongClass(): void
    {
        $object = new Environment(3);
        $class = SomeInterface::class;

        $exception = new ClassDoesNotMatchForInjectionException($object, $class);
        $this->expectExceptionObject($exception);

        $this->classInstantiator->injectObject($object, $class);
    }

    public function testStoreObjectWithAttribute(): void
    {
        $class = InterfaceToStore::class;
        $object1 = $this->classInstantiator->instantiateClass($class);
        $object2 = $this->classInstantiator->instantiateClass($class);
        self::assertSame($object1, $object2);
    }

    public function testNotStoreObjectWithAttribute(): void
    {
        $class = SomeInterface2::class;
        $object1 = $this->classInstantiator->instantiateClass($class);
        $object2 = $this->classInstantiator->instantiateClass($class);
        self::assertNotSame($object1, $object2);
    }

    public function testAttributeWithOnlyStore(): void
    {
        $class = ClassWithOnlyStore::class;
        $object1 = $this->classInstantiator->instantiateClass($class);
        $object2 = $this->classInstantiator->instantiateClass($class);
        self::assertSame($object1, $object2);
    }

    public function testExtensionHasHigherPriorityThenAttribute(): void
    {
        $class = ClassForExtensionHasHigherPriorityThenAttribute::class;
        $object1 = $this->classInstantiatorExtended->instantiateClass($class);
        $object2 = $this->classInstantiatorExtended->instantiateClass($class);
        self::assertNotSame($object1, $object2);
    }

    public function testExtensionHasHigherPriorityThenAttributeWithUseStore(): void
    {
        $class = ClassForExtensionHasHigherPriorityThenAttribute2::class;
        $object1 = $this->classInstantiatorExtended->instantiateClass($class);
        $object2 = $this->classInstantiatorExtended->instantiateClass($class);
        self::assertSame($object1, $object2);
    }

    public function testClassWithDependencyOfIntegerClassUsesAttributeOnConstructorProperty(): void
    {
        $expected = new ClassWithDependencyOfIntegerClass2(new ClassWithIntegerParameter(5));
        $actual = $this->classInstantiator->instantiateClass(ClassWithDependencyOfIntegerClass2::class);
        $this->assertEquals($expected, $actual);
    }

    public function testClassWithDependencyOfIntegerClassUsesAttributeOnConstructorPropertyWithSpecificMethod(): void
    {
        $expected = new ClassWithDependencyOfIntegerClass3(new ClassWithIntegerParameter(55));
        $actual = $this->classInstantiator->instantiateClass(ClassWithDependencyOfIntegerClass3::class);
        $this->assertEquals($expected, $actual);
    }

    public function testClassWithDependencyOfIntegerClassUsesAttributeOnConstructorPropertyWithWrongMethod(): void
    {
        $instantiatorClass = ClassInstantiatorExtended::class;

        $message = "Method $instantiatorClass::asdsdasd in attribute does not exist or is not callable.";
        $this->expectExceptionObject(new InstantiatorAttributeException($message));

        $this->classInstantiator->instantiateClass(ClassWithDependencyOfIntegerClass4::class);
    }

    public function testClassWithDependencyOfIntegerClassUsesAttributeOnConstructorPropertyWithSpecificMethodReturnsWrongObject(): void
    {
        $instantiatorClass = ClassInstantiatorExtended::class;
        $class = ClassWithIntegerParameter::class;

        $message = "Method $instantiatorClass::createSome in attribute did not return instance of $class.";
        $this->expectExceptionObject(new InstantiatorAttributeException($message));

        $this->classInstantiator->instantiateClass(ClassWithDependencyOfIntegerClass5::class);
    }

    /* HELPERS */

    private function attributesAreSupported(): bool
    {
        $phpversion = (int)PHP_VERSION;
        return $phpversion >= 8;
    }
}
