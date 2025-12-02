<?php declare(strict_types=1);

namespace HJerichen\ClassInstantiator;

use HJerichen\ClassInstantiator\Exception\InstantiatorAttributeException;
use ReflectionClass;

/** @internal  */
class ClassInstantiatorWithSpecificMethod extends ClassInstantiator
{
    public function __construct(
        private readonly ClassInstantiator $classInstantiator,
        private readonly string $method,
    ) {
        parent::__construct();
    }

    /**
     * @template T of object
     * @param ReflectionClass<T> $class
     * @param array<string, mixed> $predefinedArguments
     * @return T
     * @noinspection PhpDocSignatureInspection
     * @psalm-suppress MixedAssignment
     */
    #[\Override]
    public function instantiateClassFromReflection(ReflectionClass $class, array $predefinedArguments = []): object
    {
        $callable = [$this->classInstantiator, $this->method];
        $this->checkIsCallable($callable);

        $methodInvoker = new MethodInvoker($this->classInstantiator);
        /** @var T $object */
        $object = $methodInvoker->invokeMethod($callable, $predefinedArguments);
        $this->checkForCorrectInstance($class, $object);
        return $object;
    }

    private function checkIsCallable(array $callable): void
    {
        if (!is_callable($callable)) {
            $classInstantiatorClassName = get_class($this->classInstantiator);
            $method = "$classInstantiatorClassName::$this->method";
            throw new InstantiatorAttributeException("Method $method in attribute does not exist or is not callable.");
        }
    }

    private function checkForCorrectInstance(ReflectionClass $class, mixed $object): void
    {
        $className = $class->getName();
        if (!($object instanceof $className)) {
            $classInstantiatorClassName = get_class($this->classInstantiator);
            $method = "$classInstantiatorClassName::$this->method";
            throw new InstantiatorAttributeException("Method $method in attribute did not return instance of $className.");
        }
    }
}
