<?php declare(strict_types=1);

namespace HJerichen\ClassInstantiator\Test\Helpers;

/**
 * @author Heiko Jerichen <heiko@jerichen.de>
 */
class SomeInterface2Implementation implements SomeInterface2
{
    /** @var int */
    private $id;

    public function __construct(int $id)
    {
        $this->id = $id;
    }
}