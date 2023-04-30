<?php declare(strict_types=1);

namespace HJerichen\ClassInstantiator\FromReflection;

use HJerichen\ClassInstantiator\Attribute\Instantiator as InstantiatorAttribute;
use HJerichen\ClassInstantiator\ClassInstantiator;
use HJerichen\ClassInstantiator\Exception\InstantiatorAnnotationException;
use HJerichen\ClassInstantiator\ObjectStore;
use ReflectionClass;
use Throwable;

/**
 * @author Heiko Jerichen <heiko@jerichen.de>
 */
class ReflectionClassInstantiatorWithAttribute implements ReflectionClassInstantiator
{
    private ?InstantiatorAttribute $attribute;
    private ClassInstantiator $instantiator;
    private ReflectionClass $class;

    public function __construct(
        private readonly ReflectionClassInstantiator $reflectionClassInstantiator,
        private readonly ClassInstantiator $instantiatorOfInstantiator,
        private readonly ObjectStore $objectStore
    ) {
    }

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
            throw $this->exceptionForAnnotationException($exception);
        }
    }

    private function loadInstantiatorFromAttribute(): void
    {
        if (!isset($this->attribute)) return;
        if (!isset($this->attribute->class)) return;
        if ($this->attribute->class === get_class($this->instantiatorOfInstantiator)) return;

        $instantiator = $this->instantiatorOfInstantiator->instantiateClass($this->attribute->class);
        if (!($instantiator instanceof ClassInstantiator)) {
            throw $this->exceptionForNotAClassInstantiator();
        }
        $this->instantiator = $instantiator;
    }

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

    private function exceptionForNotAClassInstantiator(): InstantiatorAnnotationException
    {
        $message = 'Invalid value for Attribute "Instantiator": Value in class %s is not an instance of ClassInstantiator';
        $message = sprintf($message, $this->class->getName());
        return new InstantiatorAnnotationException($message);
    }

    private function exceptionForAnnotationException(Throwable $exception): InstantiatorAnnotationException
    {
        $message = 'Invalid value for Attribute "Instantiator": %s';
        $message = sprintf($message, $exception->getMessage());
        throw new InstantiatorAnnotationException($message, 0, $exception);
    }
}