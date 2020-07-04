<?php declare(strict_types=1);

namespace HJerichen\ClassInstantiator\Test\Unit\Exception;

use HJerichen\ClassInstantiator\Exception\UnknownClassException;
use PHPUnit\Framework\TestCase;

/**
 * @author Heiko Jerichen <heiko@jerichen.de>
 */
class UnknownClassExceptionTest extends TestCase
{
    /**
     * @var UnknownClassException
     */
    private $exception;
    /**
     * @var string
     */
    private $class = 'Test';

    /**
     *
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->exception = new UnknownClassException($this->class);
    }


    /* TESTS */

    public function testMessage(): void
    {
        $expected = 'Class "Test" not found.';
        $actual = $this->exception->getMessage();
        $this->assertEquals($expected, $actual);
    }


    /* HELPERS */
}
