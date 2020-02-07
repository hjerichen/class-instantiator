<?php

namespace HJerichen\ObjectFactory;

use Exception;
use HJerichen\ObjectFactory\Exception\InstantiateParameterException;
use HJerichen\ObjectFactory\TestHelpers\ClassInstantiatorExtended;
use HJerichen\ObjectFactory\TestHelpers\ClassWithIntegerParameter;
use HJerichen\ObjectFactory\TestHelpers\ClassWithSimpleDependency;
use HJerichen\ObjectFactory\TestHelpers\ClassWithTwoIntegerParameters;
use HJerichen\ObjectFactory\TestHelpers\SimpleClass;
use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ObjectProphecy;
use TypeError;

/**
 * @author Heiko Jerichen <heiko@jerichen.de>
 */
class MethodInvokerTest extends TestCase
{
    /**
     * @var MethodInvoker
     */
    private $methodInvoker;
    /**
     * @var ClassInstantiator | ObjectProphecy
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
        $this->classInstantiator = $this->prophesize(ClassInstantiator::class);

        $this->methodInvoker = new MethodInvoker($this->classInstantiator->reveal());
        $this->classInstantiatorExtended = new ClassInstantiatorExtended();
    }


    /* TESTS */

    public function testWithoutParameters(): void
    {
        $value1 = 5;
        $arguments = ['value' => 10, 'integer' => 40];
        $methodCallable = [$this->classInstantiatorExtended, 'createIntegerObject'];

        $expected = new ClassWithIntegerParameter($value1);
        $actual = $this->methodInvoker->invokeMethod($methodCallable, $arguments);
        $this->assertEquals($expected, $actual);
    }

    public function testWithParameter(): void
    {
        $value1 = 5;
        $arguments = ['value' => 10, 'integer' => 40];
        $methodCallable = [$this->classInstantiatorExtended, 'createIntegerObjectWithParameter'];

        $expected = new ClassWithTwoIntegerParameters($value1, $arguments['value']);
        $actual = $this->methodInvoker->invokeMethod($methodCallable, $arguments);
        $this->assertEquals($expected, $actual);
    }

    public function testWithMissingParameter(): void
    {
        $methodCallable = [$this->classInstantiatorExtended, 'createIntegerObjectWithParameter'];

        $this->expectException(InstantiateParameterException::class);

        $this->methodInvoker->invokeMethod($methodCallable);
    }

    public function testWithWrongMethodName(): void
    {
        $methodCallable = [$this->classInstantiatorExtended, 'something'];

        $this->expectException(TypeError::class);

        $this->methodInvoker->invokeMethod($methodCallable);
    }

    public function testWithParameterToInstantiate(): void
    {
        $arguments = ['value' => 10, 'integer' => 40];
        $methodCallable = [$this->classInstantiatorExtended, 'createObjectWithDependency'];
        $simpleClass = $this->setUpClassInstantiatorCreatesSimpleClass($arguments);

        $expected = new ClassWithSimpleDependency($simpleClass);
        $actual = $this->methodInvoker->invokeMethod($methodCallable, $arguments);
        $this->assertEquals($expected, $actual);
    }

    public function testWithClassInstantiatorThrowsException(): void
    {
        $methodCallable = [$this->classInstantiatorExtended, 'createObjectWithDependency'];
        $exception = $this->setUpClassInstantiatorThrowsException();

        $this->expectExceptionObject($exception);

        $this->methodInvoker->invokeMethod($methodCallable);
    }


    /* HELPERS */

    private function setUpClassInstantiatorCreatesSimpleClass(array $predefinedArguments): SimpleClass
    {
        $simpleClass = new SimpleClass();
        $this->classInstantiator->instantiateClass(SimpleClass::class, $predefinedArguments)->willReturn($simpleClass);
        return $simpleClass;
    }

    private function setUpClassInstantiatorThrowsException(): Exception
    {
        $exception = $this->prophesize(InstantiateParameterException::class)->reveal();
        $this->classInstantiator->instantiateClass(SimpleClass::class, [])->willThrow($exception);
        return $exception;
    }
}
