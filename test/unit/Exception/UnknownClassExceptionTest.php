<?php

namespace HJerichen\ObjectFactory\Exception;

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
