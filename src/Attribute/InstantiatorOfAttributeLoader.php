<?php declare(strict_types=1);

namespace HJerichen\ClassInstantiator\Attribute;

use HJerichen\ClassInstantiator\Attribute\Instantiator as InstantiatorAttribute;
use HJerichen\ClassInstantiator\ClassInstantiator;
use HJerichen\ClassInstantiator\ClassInstantiatorWithSpecificMethod;
use HJerichen\ClassInstantiator\Exception\InstantiatorAttributeException;
use ReflectionAttribute;

readonly class InstantiatorOfAttributeLoader
{
    public function __construct(
        private ClassInstantiator $instantiator
    ) {
    }

    /** @param ReflectionAttribute[] $reflectionAttributes */
    public function executeForAttributeList(array $reflectionAttributes): ?ClassInstantiator
    {
        foreach ($reflectionAttributes as $reflectionAttribute) {
            $attribute = $reflectionAttribute->newInstance();
            if ($attribute instanceof Instantiator) {
                return $this->execute($attribute);
            }
        }
        return null;
    }

    public function execute(InstantiatorAttribute $attribute): ?ClassInstantiator
    {
        if (!isset($attribute->class)) return null;
        if ($attribute->class === get_class($this->instantiator)) return null;

        $instantiator = $this->instantiator->instantiateClass($attribute->class);
        if (!($instantiator instanceof ClassInstantiator)) {
            throw $this->exceptionForNotAClassInstantiator($attribute->class);
        }

        if ($attribute->method !== null && trim($attribute->method) !== '') {
            $instantiator = new ClassInstantiatorWithSpecificMethod($instantiator, $attribute->method);
        }
        return $instantiator;
    }

    private function exceptionForNotAClassInstantiator(string $instantiatorClass): InstantiatorAttributeException
    {
        $message = 'Invalid value for Attribute "Instantiator": Value %s is not an instance of ClassInstantiator';
        $message = sprintf($message, $instantiatorClass);
        return new InstantiatorAttributeException($message);
    }
}
