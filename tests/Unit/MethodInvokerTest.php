<?php declare(strict_types=1);

namespace HJerichen\ClassInstantiator\Test\Unit;

use Exception;
use HJerichen\ClassInstantiator\ClassInstantiator;
use HJerichen\ClassInstantiator\Exception\InstantiateParameterException;
use HJerichen\ClassInstantiator\MethodInvoker;
use HJerichen\ClassInstantiator\Test\Helpers\ClassInstantiatorExtended;
use HJerichen\ClassInstantiator\Test\Helpers\ClassMethodWithParameterWhoHasAnnotation;
use HJerichen\ClassInstantiator\Test\Helpers\ClassWithIntegerParameter;
use HJerichen\ClassInstantiator\Test\Helpers\ClassWithSimpleDependency;
use HJerichen\ClassInstantiator\Test\Helpers\ClassWithTwoIntegerParameters;
use HJerichen\ClassInstantiator\Test\Helpers\Environment;
use HJerichen\ClassInstantiator\Test\Helpers\SimpleClass;
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

    public function testInvokingFunctionInsteadOfMethod(): void
    {
        $this->expectException(TypeError::class);
        $this->expectExceptionMessage('Failed creating reflection method:');

        $methodCallable = static function() {};
        $this->methodInvoker->invokeMethod($methodCallable);
    }

    public function testInvokeMethodWithParameterHasAnnotation(): void
    {
        $classInstantiator = new ClassInstantiator();
        $methodInvoker = new MethodInvoker($classInstantiator);

        $class = new ClassMethodWithParameterWhoHasAnnotation();
        $callable = [$class, 'method'];

        $actual = $methodInvoker->invokeMethod($callable);
        $expected = new Environment(4);
        $this->assertEquals($expected, $actual);
    }

    public function testInvokeMethodWithStringForIntParameter(): void
    {
        $callable = [$this->classInstantiatorExtended, 'createIntegerObjectWithParameter'];

        $expected = new ClassWithTwoIntegerParameters(5, 10);
        $actual = $this->methodInvoker->invokeMethod($callable, ['value' => '10']);
        $this->assertEquals($expected, $actual);
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
