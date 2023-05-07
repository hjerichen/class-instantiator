<?php declare(strict_types=1);

namespace HJerichen\ClassInstantiator\Test\Unit\Exception;

use HJerichen\ClassInstantiator\Exception\ClassInstantiatorException;
use HJerichen\ClassInstantiator\Exception\InstantiateParameterException;
use HJerichen\ClassInstantiator\Test\Helpers\ClassWithIntegerParameter;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

/**
 * @author Heiko Jerichen <heiko@jerichen.de>
 */
class InstantiateParameterExceptionTest extends TestCase
{
    private InstantiateParameterException $exception;

    /** @psalm-suppress PossiblyNullReference */
    protected function setUp(): void
    {
        parent::setUp();
        $reflectionClass = $this->createReflectionClassForTest();
        $reflectionParameter = $reflectionClass->getConstructor()->getParameters()[0];

        $this->exception = new InstantiateParameterException($reflectionParameter);
    }


    /* TESTS */

    public function testImplementsCorrectInterface(): void
    {
        self::assertInstanceOf(ClassInstantiatorException::class, $this->exception);
    }

    public function testMessage(): void
    {
        $class = ClassWithIntegerParameter::class;

        $expected = sprintf('Can\'t instantiate "int $value" for method "__construct" of class "%s"', $class);
        $actual = $this->exception->getMessage();
        self::assertEquals($expected, $actual);
    }


    /* HELPERS */

    /** @noinspection PhpUnhandledExceptionInspection */
    private function createReflectionClassForTest(): ReflectionClass
    {
        $class = ClassWithIntegerParameter::class;
        return new ReflectionClass($class);
    }
}
