<?php

namespace HJerichen\ClassInstantiator\Exception;

/**
 * @author Heiko Jerichen <heiko@jerichen.de>
 */
class ClassDoesNotMatchForInjectionException extends ClassInstantiatorException
{
    public function __construct(object $object, string $class)
    {
        $message = "Object of class %s is not instance of %s";
        $message = sprintf($message, get_class($object), $class);
        parent::__construct($message);
    }
}