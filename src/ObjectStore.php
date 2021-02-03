<?php declare(strict_types=1);

namespace HJerichen\ClassInstantiator;

use HJerichen\ClassInstantiator\Exception\ClassDoesNotMatchForInjectionException;

class ObjectStore
{
    /** @var array<string,object> */
    private static $objects = [];

    public function storeObject(object $object, ?string $class = null): void
    {
        $this->checkClassForObject($object, $class);

        $class = $class ?? get_class($object);
        self::$objects[$class] = $object;
    }

    public function checkClassForObject(object $object, ?string $class): void
    {
        if ($class === null) return;
        if ($object instanceof $class) return;

        throw new ClassDoesNotMatchForInjectionException($object, $class);
    }

    public function hasObjectForClass(string $class): bool
    {
        return isset(self::$objects[$class]);
    }

    public function retrieveObjectForClass(string $class): ?object
    {
        return self::$objects[$class];
    }

    public function flush(): void
    {
        self::$objects = [];
    }
}