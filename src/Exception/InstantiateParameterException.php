<?php

namespace HJerichen\ClassInstantiator\Exception;

use ReflectionParameter;

/**
 * @author Heiko Jerichen <heiko@jerichen.de>
 */
class InstantiateParameterException extends ClassInstantiatorException
{
    /**
     * @var ReflectionParameter
     */
    private $reflectionParameter;

    public function __construct(ReflectionParameter $reflectionParameter)
    {
        $this->reflectionParameter = $reflectionParameter;

        $message = $this->createMessage();
        parent::__construct($message);
    }

    private function createMessage(): string
    {
        $className = $this->reflectionParameter->getDeclaringClass()->getName();
        $methodName = $this->reflectionParameter->getDeclaringFunction()->getName();
        $parameterName = $this->reflectionParameter->getName();
        $parameterType = $this->reflectionParameter->getType();
        return sprintf("Can't instantiate \"%s $%s\" for method \"%s\" of class \"%s\"", $parameterType, $parameterName, $methodName, $className);
    }
}