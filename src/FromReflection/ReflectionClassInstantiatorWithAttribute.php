<?php declare(strict_types=1);

namespace HJerichen\ClassInstantiator\FromReflection;

use HJerichen\ClassInstantiator\Attribute\Instantiator as InstantiatorAttribute;
use HJerichen\ClassInstantiator\Attribute\InstantiatorOfAttributeLoader;
use HJerichen\ClassInstantiator\ClassInstantiator;
use HJerichen\ClassInstantiator\Exception\InstantiatorAttributeException;
use HJerichen\ClassInstantiator\ObjectStore;
use ReflectionClass;
use Throwable;

/**
 * @author Heiko Jerichen <heiko@jerichen.de>
 */
class ReflectionClassInstantiatorWithAttribute implements ReflectionClassInstantiator
{
    private ?InstantiatorAttribute $attribute;
    private ?ClassInstantiator $instantiator;
    private ReflectionClass $class;

    public function __construct(
        private readonly InstantiatorOfAttributeLoader $instantiatorOfAttributeLoader,
        private readonly ReflectionClassInstantiator $reflectionClassInstantiator,
        private readonly ObjectStore $objectStore
    ) {
    }

    /**
     * @template T of object
     * @param ReflectionClass<T> $reflectionClass
     * @param array<string,mixed> $predefinedArguments
     * @return T|null
     * @noinspection PhpDocSignatureInspection
     * @psalm-suppress InvalidReturnStatement
     * @psalm-suppress InvalidReturnType
     */
    public function instantiateClass(ReflectionClass $reflectionClass, array $predefinedArguments): ?object
    {
        $this->class = $reflectionClass;

        try {
            $this->loadAttribute();
            $this->loadInstantiatorFromAttribute();

            $object = $this->instantiateWith($predefinedArguments);
            $this->storeObject($object);
            return $object;
        } finally {
            $this->cleanup();
        }
    }

    private function loadAttribute(): void
    {
        try {
            $attributes = $this->class->getAttributes(InstantiatorAttribute::class);
            if (count($attributes) > 0) {
                $this->attribute = $attributes[0]->newInstance();
            }
        } catch (Throwable $exception) {
            throw $this->exceptionForAttributeException($exception);
        }
    }

    private function loadInstantiatorFromAttribute(): void
    {
        if (!isset($this->attribute)) return;
        $this->instantiator = $this->instantiatorOfAttributeLoader->execute($this->attribute);
    }

    /** @param  array<string,mixed> $predefinedArguments */
    private function instantiateWith(array $predefinedArguments): ?object
    {
        if (isset($this->instantiator)) {
           return $this->instantiator->instantiateClassFromReflection($this->class, $predefinedArguments);
        }
        return $this->reflectionClassInstantiator->instantiateClass($this->class, $predefinedArguments);
    }

    private function storeObject(?object $object): void
    {
        if ($object === null) return;
        if (!isset($this->attribute)) return;
        if ($this->attribute->store === false) return;

        $this->objectStore->storeObject($object, $this->class->getName());
    }

    private function cleanup(): void
    {
        unset($this->attribute, $this->instantiator, $this->class);
    }

    private function exceptionForAttributeException(Throwable $exception): InstantiatorAttributeException
    {
        $message = 'Invalid value for Attribute "Instantiator": %s';
        $message = sprintf($message, $exception->getMessage());
        throw new InstantiatorAttributeException($message, 0, $exception);
    }
}