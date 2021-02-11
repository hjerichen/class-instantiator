<?php declare(strict_types=1);

namespace HJerichen\ClassInstantiator\FromReflection;

use Doctrine\Common\Annotations\AnnotationException;
use Doctrine\Common\Annotations\AnnotationReader;
use HJerichen\ClassInstantiator\Annotation\Instantiator as InstantiatorAnnotation;
use HJerichen\ClassInstantiator\ClassInstantiator;
use HJerichen\ClassInstantiator\Exception\InstantiatorAnnotationException;
use ReflectionClass;

/**
 * @author Heiko Jerichen <heiko@jerichen.de>
 */
class ReflectionClassInstantiatorWithAnnotation implements ReflectionClassInstantiator
{
    private ReflectionClassInstantiator $reflectionClassInstantiator;
    private ClassInstantiator $instantiatorOfInstantiator;

    private ?InstantiatorAnnotation $annotation;
    private ClassInstantiator $instantiator;
    private ReflectionClass $class;

    public function __construct(
        ClassInstantiator $instantiatorOfInstantiator,
        ReflectionClassInstantiator $reflectionClassInstantiator
    ) {
        $this->reflectionClassInstantiator = $reflectionClassInstantiator;
        $this->instantiatorOfInstantiator = $instantiatorOfInstantiator;
    }

    public function instantiateClass(ReflectionClass $reflectionClass, array $predefinedArguments): ?object
    {
        $this->class = $reflectionClass;

        try {
            $this->loadAnnotation();
            $this->loadInstantiatorFromAnnotation();
            return $this->instantiateWith($predefinedArguments);
        } finally {
            $this->cleanup();
        }
    }

    /** @noinspection PhpRedundantCatchClauseInspection */
    private function loadAnnotation(): void
    {
        try {
            $reader = new AnnotationReader();
            $annotationName = InstantiatorAnnotation::class;
            $annotation = $reader->getClassAnnotation($this->class, $annotationName);
            if ($annotation) $this->annotation = $annotation;
        } catch (AnnotationException $exception) {
            throw $this->exceptionForAnnotationException($exception);
        }
    }

    private function loadInstantiatorFromAnnotation(): void
    {
        if (!isset($this->annotation)) return;
        if ($this->annotation->class === get_class($this->instantiatorOfInstantiator)) return;

        $instantiator = $this->instantiatorOfInstantiator->instantiateClass($this->annotation->class);
        if (!($instantiator instanceof ClassInstantiator)) {
            throw $this->exceptionForNotAClassInstantiator();
        }
        $this->instantiator = $instantiator;
    }

    private function instantiateWith(array $predefinedArguments): ?object
    {
        if (!isset($this->instantiator)) {
            return $this->reflectionClassInstantiator->instantiateClass($this->class, $predefinedArguments);
        }

        $object = $this->instantiator->instantiateClassFromReflection($this->class, $predefinedArguments);
        if ($this->annotation->store) {
            $this->instantiator->injectObject($object, $this->class->getName());
        }
        return $object;
    }

    private function cleanup(): void
    {
        unset($this->annotation, $this->instantiator, $this->class);
    }

    private function exceptionForNotAClassInstantiator(): InstantiatorAnnotationException
    {
        $message = 'Invalid value for Annotation "Instantiator": Value in class %s is not an instance of ClassInstantiator';
        $message = sprintf($message, $this->class->getName());
        return new InstantiatorAnnotationException($message);
    }

    private function exceptionForAnnotationException(AnnotationException $exception): InstantiatorAnnotationException
    {
        $message = 'Invalid value for Annotation "Instantiator": %s';
        $message = sprintf($message, $exception->getMessage());
        throw new InstantiatorAnnotationException($message, 0, $exception);
    }
}