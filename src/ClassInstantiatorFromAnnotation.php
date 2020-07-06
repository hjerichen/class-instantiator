<?php declare(strict_types=1);

namespace HJerichen\ClassInstantiator;

use Doctrine\Common\Annotations\AnnotationException;
use Doctrine\Common\Annotations\AnnotationReader;
use HJerichen\ClassInstantiator\Annotation\Instantiator;
use HJerichen\ClassInstantiator\Exception\InstantiatorAnnotationException;
use ReflectionClass;

/**
 * @author Heiko Jerichen <heiko@jerichen.de>
 */
class ClassInstantiatorFromAnnotation
{
    /** @var ReflectionClass */
    private $reflectionClass;

    /** @var ClassInstantiator */
    private $instantiatorOfInstantiator;

    public function __construct(ClassInstantiator $instantiatorOfInstantiator, ReflectionClass $reflectionClass)
    {
        $this->reflectionClass = $reflectionClass;
        $this->instantiatorOfInstantiator = $instantiatorOfInstantiator;
    }

    public function instantiateClass(array $predefinedArguments): ?object
    {
        $instantiator = $this->getInstantiatorForReflectionClass();
        if ($instantiator === null) return null;

        return $instantiator->instantiateClassFromReflection($this->reflectionClass, $predefinedArguments);
    }

    private function getInstantiatorForReflectionClass(): ?ClassInstantiator
    {
        $annotation = $this->getInstantiatorAnnotationFromReflectionClass();
        if ($annotation === null) return null;

        if (get_class($this->instantiatorOfInstantiator) === $annotation->class) {
            return null;
        }

        $instantiator = $this->instantiatorOfInstantiator->instantiateClass($annotation->class);
        if (!($instantiator instanceof ClassInstantiator)) {
            throw $this->exceptionForNotAClassInstantiator();
        }

        return $instantiator;
    }

    /** @noinspection PhpRedundantCatchClauseInspection */
    private function getInstantiatorAnnotationFromReflectionClass(): ?Instantiator
    {
        try {
            $reader = new AnnotationReader();
            return $reader->getClassAnnotation($this->reflectionClass, Instantiator::class);
        } catch (AnnotationException $exception) {
            throw $this->exceptionForAnnotationException($exception);
        }
    }

    private function exceptionForNotAClassInstantiator(): InstantiatorAnnotationException
    {
        $message = 'Invalid value for Annotation "Instantiator": Value in class %s is not an instance of ClassInstantiator';
        $message = sprintf($message, $this->reflectionClass->getName());
        return new InstantiatorAnnotationException($message);
    }

    private function exceptionForAnnotationException(AnnotationException $exception): InstantiatorAnnotationException
    {
        $message = 'Invalid value for Annotation "Instantiator": %s';
        $message = sprintf($message, $exception->getMessage());
        throw new InstantiatorAnnotationException($message, 0, $exception);
    }
}