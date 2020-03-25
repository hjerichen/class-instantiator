<?php declare(strict_types=1);

namespace HJerichen\ClassInstantiator\Exception;

/**
 * @author Heiko Jerichen <heiko@jerichen.de>
 */
class UnknownClassException extends ClassInstantiatorException
{
    public function __construct(string $class)
    {
        $message = "Class \"{$class}\" not found.";
        parent::__construct($message);
    }
}