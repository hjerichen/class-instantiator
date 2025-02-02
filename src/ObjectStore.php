<?php declare(strict_types=1);

namespace HJerichen\ClassInstantiator;

use HJerichen\ClassInstantiator\Exception\ClassDoesNotMatchForInjectionException;

class ObjectStore
{
    /** @var array<string,object> */
    private static array $objects = [];

    /** @param class-string|null $class */
    public function storeObject(object $object, ?string $class = null): void
    {
        $this->checkClassForObject($object, $class);

        $class = $class ?? get_class($object);
        self::$objects[$class] = $object;
    }

    /** @param class-string|null $class */
    public function checkClassForObject(object $object, ?string $class): void
    {
        if ($class === null) return;
        if ($object instanceof $class) return;

        throw new ClassDoesNotMatchForInjectionException($object, $class);
    }

    /** @param class-string $class */
    public function hasObjectForClass(string $class): bool
    {
        return isset(self::$objects[$class]);
    }

    /**
     * @template T
     * @param class-string<T> $class
     * @return T|null
     */
    public function retrieveObjectForClass(string $class): ?object
    {
        $object = self::$objects[$class] ?? null;
        return ($object instanceof $class) ? $object : null;
    }

    public function flush(): void
    {
        self::$objects = [];
    }
}