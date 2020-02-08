<?php

namespace HJerichen\ClassInstantiator;

use ReflectionException;
use ReflectionMethod;
use TypeError;

/**
 * @author Heiko Jerichen <heiko@jerichen.de>
 */
class MethodInvoker
{
    /**
     * @var ClassInstantiator
     */
    private $classInstantiator;
    /**
     * @var array|callable
     */
    private $methodCallable;

    public function __construct(ClassInstantiator $classInstantiator)
    {
        $this->classInstantiator = $classInstantiator;
    }

    /**
     * @param array|callable $methodCallable
     * @param array $predefinedArguments
     * @return mixed
     */
    public function invokeMethod(array $methodCallable, array $predefinedArguments = [])
    {
        $this->methodCallable = $methodCallable;

        $parameters = $this->buildParameters($predefinedArguments);
        $reflectionMethod = $this->createReflectionMethod();
        $reflectionMethod->setAccessible(true);
        return $reflectionMethod->invokeArgs($this->methodCallable[0], $parameters);
    }

    private function buildParameters(array $predefinedArguments): array
    {
        $reflectionMethod = $this->createReflectionMethod();
        $argumentsBuilder = new ArgumentsForMethodBuilder($this->classInstantiator, $reflectionMethod);
        $argumentsBuilder->setPredefinedArguments($predefinedArguments);
        return $argumentsBuilder->buildArguments();
    }

    private function createReflectionMethod(): ReflectionMethod
    {
        try {
            [$object, $method] = $this->methodCallable;
            return new ReflectionMethod($object, $method);
        } catch(ReflectionException $exception) {
            $message = sprintf('Invalid callable [%s, %s]', get_class($object), $method);
            throw new TypeError($message);
        }
    }
}