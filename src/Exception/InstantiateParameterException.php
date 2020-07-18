<?php declare(strict_types=1);

namespace HJerichen\ClassInstantiator\Exception;

use Psr\Container\ContainerExceptionInterface;
use ReflectionParameter;

/**
 * @author Heiko Jerichen <heiko@jerichen.de>
 */
class InstantiateParameterException extends ClassInstantiatorException implements ContainerExceptionInterface
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

        /** @noinspection PhpPossiblePolymorphicInvocationInspection */
        $parameterTypeName = $parameterType ? $parameterType->getName() : '';

        return sprintf("Can't instantiate \"%s $%s\" for method \"%s\" of class \"%s\"", $parameterTypeName, $parameterName, $methodName, $className);
    }
}