<?php declare(strict_types=1);

namespace HJerichen\ClassInstantiator;

use Psr\Container\ContainerInterface;

class ClassInstantiatorContainer implements ContainerInterface
{
    private ClassInstantiator $classInstantiator;
    /** @var object[] */
    private array $entries = [];

    public function __construct(ClassInstantiator $classInstantiator)
    {
        $this->classInstantiator = $classInstantiator;
    }

    public function has($id): bool
    {
        return class_exists($id);
    }

    public function get($id): object
    {
        $this->loadEntry($id);
        return $this->entries[$id];
    }

    private function loadEntry($id): void
    {
        if (!array_key_exists($id, $this->entries)) {
            $this->entries[$id] = $this->classInstantiator->instantiateClass($id);
        }
    }
}