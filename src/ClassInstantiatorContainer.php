<?php declare(strict_types=1);

namespace HJerichen\ClassInstantiator;

use Psr\Container\ContainerInterface;

class ClassInstantiatorContainer implements ContainerInterface
{
    /** @var array<string,object> */
    private array $entries = [];

    public function __construct(
        private readonly ClassInstantiator $classInstantiator
    ) {
    }

    public function has(string $id): bool
    {
        return class_exists($id);
    }

    public function get(string $id): object
    {
        $this->loadEntry($id);
        return $this->entries[$id];
    }

    private function loadEntry(string $id): void
    {
        if (!array_key_exists($id, $this->entries)) {
            $this->entries[$id] = $this->classInstantiator->instantiateClass($id);
        }
    }
}