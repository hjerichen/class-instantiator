<?php declare(strict_types=1);

namespace HJerichen\ClassInstantiator\Exception;

use Psr\Container\ContainerExceptionInterface;
use ReflectionParameter;

/**
 * @author Heiko Jerichen <heiko@jerichen.de>
 */
class InstantiateParameterException extends ClassInstantiatorException implements ContainerExceptionInterface
{
    public function __construct(
        private readonly ReflectionParameter $reflectionParameter
    ) {
        $message = $this->createMessage();
        parent::__construct($message);
    }

    private function createMessage(): string
    {
        /** @noinspection NullPointerExceptionInspection */
        $className = $this->reflectionParameter->getDeclaringClass()->getName();
        $methodName = $this->reflectionParameter->getDeclaringFunction()->getName();
        $parameterName = $this->reflectionParameter->getName();
        $parameterType = $this->reflectionParameter->getType();

        $parameterTypeName = $parameterType?->getName() ?? '';

        $message = "Can't instantiate \"%s $%s\" for method \"%s\" of class \"%s\"";
        return sprintf($message, $parameterTypeName, $parameterName, $methodName, $className);
    }
}