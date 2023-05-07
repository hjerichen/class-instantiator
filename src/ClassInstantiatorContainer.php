<?php declare(strict_types=1);

namespace HJerichen\ClassInstantiator;

use Psr\Container\ContainerInterface;

class ClassInstantiatorContainer implements ContainerInterface
{
    /** @var array<class-string,object> */
    private array $entries = [];

    public function __construct(
        private readonly ClassInstantiator $classInstantiator
    ) {
    }

    /**
     * @param class-string $id
     * @psalm-suppress MoreSpecificImplementedParamType
     */
    public function has(string $id): bool
    {
        return class_exists($id);
    }

    /**
     * @template T of object
     * @param class-string<T> $id
     * @return T
     * @noinspection PhpDocSignatureInspection
     * @psalm-suppress MoreSpecificImplementedParamType
     * @psalm-suppress InvalidReturnStatement
     * @psalm-suppress InvalidReturnType
     */
    public function get(string $id): object
    {
        $this->loadEntry($id);
        return $this->entries[$id];
    }

    /** @param class-string $id */
    private function loadEntry(string $id): void
    {
        if (!array_key_exists($id, $this->entries)) {
            $this->entries[$id] = $this->classInstantiator->instantiateClass($id);
        }
    }
}