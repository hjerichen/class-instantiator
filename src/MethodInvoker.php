<?php declare(strict_types=1);

namespace HJerichen\ClassInstantiator;

use HJerichen\ClassInstantiator\ArgumentBuilder\ArgumentsForMethodBuilder;
use ReflectionMethod;
use Throwable;
use TypeError;

/**
 * @author Heiko Jerichen <heiko@jerichen.de>
 */
class MethodInvoker
{
    /** @var callable */
    private $methodCallable;

    public function __construct(
        private readonly ClassInstantiator $classInstantiator
    ) {
    }

    public function invokeMethod(callable $methodCallable, array $predefinedArguments = [])
    {
        $this->methodCallable = $methodCallable;

        $parameters = $this->buildParameters($predefinedArguments);
        return $methodCallable(...$parameters);
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
        } catch(Throwable $throwable) {
            $message = sprintf('Failed creating reflection method: %s', $throwable->getMessage());
            throw new TypeError($message, $throwable->getCode(), $throwable);
        }
    }
}